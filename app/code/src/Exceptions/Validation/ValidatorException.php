<?php

declare(strict_types=1);

namespace App\Exceptions\Validation;

use Exception;

class ValidatorException extends Exception
{
    private readonly array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct($this->createMessage(), 42200);
    }

    private function createMessage(): string
    {
        $message = 'Validation errors:'. PHP_EOL;

        foreach ($this->getErrors() as $error) {
            $message .= sprintf(
                "[%s] - %s %s",
                $error['property'],
                $error['message'],
                PHP_EOL,
            );
        }

        return $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
