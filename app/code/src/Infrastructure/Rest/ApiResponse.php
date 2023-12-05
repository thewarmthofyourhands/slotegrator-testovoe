<?php

declare(strict_types=1);

namespace App\Infrastructure\Rest;

use JsonException;
use Symfony\Component\HttpFoundation\Response;

readonly class ApiResponse
{
    public function __construct(
        private null|array $data = null,
        private int $code = 20000,
        private string $message = 'ok',
        private array $headers = [
            'Content-Type' => 'application/json',
        ],
    ) {}

    /**
     * @throws JsonException
     */
    public function build(int $httpCode = 200): Response
    {
        $data = [
            'data' => $this->data,
            'message' => $this->message,
            'code' => $this->code,
        ];

        return new Response(
            json_encode($data, JSON_THROW_ON_ERROR),
            $httpCode,
            $this->headers,
        );
    }
}
