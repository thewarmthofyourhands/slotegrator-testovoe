<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IdAwareEntityTrait
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="bigint", nullable=false, options={"unsigned": true})
     */
    protected int $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
