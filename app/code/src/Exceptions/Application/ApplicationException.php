<?php

declare(strict_types=1);

namespace App\Exceptions\Application;

use Throwable;
use Exception;

class ApplicationException extends Exception
{
    public function __construct(ApplicationErrorCodeEnum $code, ?Throwable $previous = null)
    {
        parent::__construct($this->getMessageByCode($code)->value, $code->value, $previous);
    }

    private function getMessageByCode(ApplicationErrorCodeEnum $code): ApplicationErrorMessagesEnum
    {
        return ApplicationErrorMessagesEnum::fromName($code->name);
    }
}
