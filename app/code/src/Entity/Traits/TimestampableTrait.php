<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

use DateTime;

trait TimestampableTrait
{
    /**
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected DateTime $createdAt;

    /**
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    protected DateTime $updatedAt;

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}
