<?php

declare(strict_types=1);

namespace App\Exceptions\Application;

use ValueError;

enum ApplicationErrorMessagesEnum: string
{
    case PRODUCT_NOT_FOUND = 'Product not found';
    case CATEGORY_NOT_FOUND = 'Category not found';

    public static function fromName(string $name): self
    {
        foreach (self::cases() as $status) {
            if ($name === $status->name){
                return $status;
            }
        }

        throw new ValueError("$name is not a valid backing value for enum " . self::class );
    }
}
