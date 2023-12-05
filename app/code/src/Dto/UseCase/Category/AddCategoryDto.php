<?php

declare(strict_types=1);

namespace App\Dto\UseCase\Category;

final readonly class AddCategoryDto
{
    public function __construct(
        private string $title,
        private null|int $eId,
    ) {}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getEId(): null|int
    {
        return $this->eId;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
