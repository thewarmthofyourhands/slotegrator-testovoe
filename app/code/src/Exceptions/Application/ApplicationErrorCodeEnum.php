<?php

declare(strict_types=1);

namespace App\Exceptions\Application;

enum ApplicationErrorCodeEnum: int
{
    case PRODUCT_NOT_FOUND = 40401;
    case CATEGORY_NOT_FOUND = 40402;
}
