<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exceptions\Application\ApplicationException;
use App\Exceptions\Validation\ValidatorException;
use App\Infrastructure\Rest\ApiResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

readonly class HttpExceptionListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ApplicationException) {
            $event->setResponse((new ApiResponse(
                null,
                $exception->getCode(),
                $exception->getMessage(),
            ))->build(400));
        } else if ($exception instanceof ValidatorException) {
            $event->setResponse((new ApiResponse(
                null,
                $exception->getCode(),
                $exception->getMessage(),
            ))->build(422));
        } else {
            $event->setResponse((new ApiResponse(
                null,
                500,
                'Something went wrong',
            ))->build(500));
        }
        $this->logger->error($exception->getMessage(), ['exception' => $exception]);
    }
}
