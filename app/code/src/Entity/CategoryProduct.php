<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="CategoryProduct")
 * @ORM\Entity()
 */
class CategoryProduct
{
    /**
     * @ORM\Column(type="bigint", nullable=false, options={"unsigned": true})
     */
    private int $categoryEId;

    /**
     * @ORM\Column(type="bigint", nullable=false, options={"unsigned": true})
     */
    private int $productEId;
}
