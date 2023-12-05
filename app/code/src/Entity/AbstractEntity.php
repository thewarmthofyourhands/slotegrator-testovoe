<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Entity\Traits\IdAwareEntityTrait;

abstract class AbstractEntity
{
    use IdAwareEntityTrait;
    use TimestampableTrait;
}
