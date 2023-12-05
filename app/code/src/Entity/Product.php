<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="Product",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"eId"})
 *     }
 * )
 * @ORM\Entity()
 */
class Product extends AbstractEntity
{
    /**
     * @ORM\Column(type="string")
     */
    private string $title;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private float $price;

    /**
     * @ORM\Column(type="bigint", nullable=true, options={"unsigned": true})
     */
    private ?int $eId;
}
