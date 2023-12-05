<?php

declare(strict_types=1);

namespace App\Dto\Services\ProductService;

final readonly class ProductDto
{
    public function __construct(
        private int $id,
        private string $title,
        private float $price,
        private null|int $eId,
        private array $categoryList,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

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

    public function getCategoryList(): array
    {
        return $this->categoryList;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
