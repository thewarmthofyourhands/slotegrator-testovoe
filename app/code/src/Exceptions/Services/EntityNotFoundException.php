<?php

declare(strict_types=1);

namespace App\Exceptions\Services;

use Exception;
use Throwable;

class EntityNotFoundException extends Exception
{
    public function __construct(int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct('Entity not found', $code, $previous);
    }
}
