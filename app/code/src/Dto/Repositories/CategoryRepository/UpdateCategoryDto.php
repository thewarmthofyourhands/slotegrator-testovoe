<?php

declare(strict_types=1);

namespace App\Dto\Repositories\CategoryRepository;

final readonly class UpdateCategoryDto
{
    public function __construct(
        private int $id,
        private string $title,
        private null|int $eId,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

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
