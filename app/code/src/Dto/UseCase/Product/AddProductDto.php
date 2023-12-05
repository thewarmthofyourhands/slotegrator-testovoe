<?php

declare(strict_types=1);

namespace App\Dto\UseCase\Product;

final readonly class AddProductDto
{
    public function __construct(
        private string $title,
        private float $price,
        private null|int $eId,
        private array $categoryEIdList,
    ) {}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getEId(): null|int
    {
        return $this->eId;
    }

    public function getCategoryEIdList(): array
    {
        return $this->categoryEIdList;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
