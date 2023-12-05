<?php

declare(strict_types=1);

namespace App\UseCase\Category;

use App\Dto\UseCase\Category\UpdateCategoryDto;
use App\Services\CategoryService;
use App\Services\Notifications\NotificationServiceInterface;

readonly class UpdateCategoryHandler
{
    public function __construct(
        private CategoryService $categoryService,
        private NotificationServiceInterface $notificationService,
    ) {}

    public function handle(UpdateCategoryDto $dto): void
    {
        $this->categoryService->updateCategory(new \App\Dto\Services\CategoryService\UpdateCategoryDto(
            $dto->getId(),
            $dto->getTitle(),
            $dto->getEId(),
        ));

        $categoryDto = $this->categoryService->getCategoryById($dto->getId());
        $this->notificationService->notify(
            $this->categoryService->dtoToText($categoryDto),
        );
    }
}
