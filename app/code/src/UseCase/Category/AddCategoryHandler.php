<?php

declare(strict_types=1);

namespace App\UseCase\Category;

use App\Dto\UseCase\Category\AddCategoryDto;
use App\Exceptions\Services\EntityNotFoundException;
use App\Services\CategoryService;
use App\Services\Notifications\NotificationServiceInterface;

readonly class AddCategoryHandler
{
    public function __construct(
        private CategoryService $categoryService,
        private NotificationServiceInterface $notificationService,
    ) {}

    /**
     * @throws EntityNotFoundException
     */
    public function handle(AddCategoryDto $dto): int
    {
        $id = $this->categoryService->addCategory(
            new \App\Dto\Services\CategoryService\AddCategoryDto(
                $dto->getTitle(),
                $dto->getEId(),
            ),
        );
        $categoryDto = $this->categoryService->getCategoryById($id);
        $this->notificationService->notify(
            $this->categoryService->dtoToText($categoryDto),
        );

        return $id;
    }
}
