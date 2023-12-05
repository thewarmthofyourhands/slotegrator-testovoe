<?php

declare(strict_types=1);

namespace App\Infrastructure\Runner;

use InvalidArgumentException;
use JsonException;
use Nyholm\Psr7;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\RoadRunner;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\Runtime\RunnerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use function is_array;

class Runner implements RunnerInterface
{
    private HttpKernelInterface $kernel;
    private Psr7\Factory\Psr17Factory $psrFactory;

    public function __construct(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->psrFactory = new Psr7\Factory\Psr17Factory();
    }

    protected function getTemporaryPath(): string
    {
        return tempnam(sys_get_temp_dir(), uniqid('symfony', true));
    }

    private function createUploadedFile(UploadedFileInterface $psrUploadedFile): UploadedFile
    {
        return new UploadedFile($psrUploadedFile, function () { return $this->getTemporaryPath(); });
    }

    private function getFiles(array $uploadedFiles): array
    {
        $files = [];

        foreach ($uploadedFiles as $key => $value) {
            if ($value instanceof UploadedFileInterface) {
                $files[$key] = $this->createUploadedFile($value);
            } else {
                $files[$key] = $this->getFiles($value);
            }
        }

        return $files;
    }

    private function createSfRequest(ServerRequestInterface $psrRequest): Request
    {
        $server = [];
        $uri = $psrRequest->getUri();

        $server['SERVER_NAME'] = $uri->getHost();
        $server['SERVER_PORT'] = $uri->getPort() ?: ('https' === $uri->getScheme() ? 443 : 80);
        $server['REQUEST_URI'] = $uri->getPath();
        $server['QUERY_STRING'] = $uri->getQuery();

        if ('' !== $server['QUERY_STRING']) {
            $server['REQUEST_URI'] .= '?'.$server['QUERY_STRING'];
        }

        if ('https' === $uri->getScheme()) {
            $server['HTTPS'] = 'on';
        }

        $server['REQUEST_METHOD'] = $psrRequest->getMethod();

        $server = array_replace($psrRequest->getServerParams(), $server);

        $parsedBody = $psrRequest->getParsedBody();
        $parsedBody = is_array($parsedBody) ? $parsedBody : [];

        $sfRequest = new Request(
            $psrRequest->getQueryParams(),
            $parsedBody,
            $psrRequest->getAttributes(),
            $psrRequest->getCookieParams(),
            $this->getFiles($psrRequest->getUploadedFiles()),
            $server,
            $psrRequest->getBody()->__toString()
        );
        $sfRequest->headers->add($psrRequest->getHeaders());

        return $sfRequest;
    }

    private function createResponseBySfResponse(Response $symfonyResponse): ResponseInterface
    {
        $response = $this->psrFactory->createResponse($symfonyResponse->getStatusCode(), Response::$statusTexts[$symfonyResponse->getStatusCode()] ?? '');

        if ($symfonyResponse instanceof BinaryFileResponse && !$symfonyResponse->headers->has('Content-Range')) {
            $stream = $this->psrFactory->createStreamFromFile(
                $symfonyResponse->getFile()->getPathname()
            );
        } else {
            $stream = $this->psrFactory->createStreamFromFile('php://temp', 'wb+');
            if ($symfonyResponse instanceof StreamedResponse || $symfonyResponse instanceof BinaryFileResponse) {
                ob_start(static function ($buffer) use ($stream) {
                    $stream->write($buffer);

                    return '';
                }, 1);

                $symfonyResponse->sendContent();
                ob_end_clean();
            } else {
                $stream->write($symfonyResponse->getContent());
            }
        }

        $response = $response->withBody($stream);

        $headers = $symfonyResponse->headers->all();
        $cookies = $symfonyResponse->headers->getCookies();
        if (!empty($cookies)) {
            $headers['Set-Cookie'] = [];

            foreach ($cookies as $cookie) {
                $headers['Set-Cookie'][] = $cookie->__toString();
            }
        }

        foreach ($headers as $name => $value) {
            try {
                $response = $response->withHeader($name, $value);
            } catch (InvalidArgumentException $e) {
                // ignore invalid header
            }
        }

        $protocolVersion = $symfonyResponse->getProtocolVersion();

        return $response->withProtocolVersion($protocolVersion);
    }

    /**
     * @throws JsonException
     */
    public function run(): int
    {
        $worker = RoadRunner\Worker::create();
        $worker = new RoadRunner\Http\PSR7Worker($worker, $this->psrFactory, $this->psrFactory, $this->psrFactory);

        while ($request = $worker->waitRequest()) {
            try {
                $sfRequest = $this->createSfRequest($request);
                $sfResponse = $this->kernel->handle($sfRequest);
                $worker->respond($this->createResponseBySfResponse($sfResponse));

                if ($this->kernel instanceof TerminableInterface) {
                    $this->kernel->terminate($sfRequest, $sfResponse);
                }
            } catch (Throwable $e) {
                $worker->getWorker()->error((string) $e);
            }
        }

        return 0;
    }
}
