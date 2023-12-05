<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="Category",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"eId"})
 *    }
 * )
 * @ORM\Entity()
 */
class Category extends AbstractEntity
{
    /**
     * @ORM\Column(type="string")
     */
    private string $title;

    /**
     * @ORM\Column(type="bigint", nullable=true, options={"unsigned": true})
     */
    private ?int $eId;
}
