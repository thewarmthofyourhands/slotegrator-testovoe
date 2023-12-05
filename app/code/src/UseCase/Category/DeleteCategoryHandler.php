<?php

declare(strict_types=1);

namespace App\UseCase\Category;

use App\Services\CategoryService;

readonly class DeleteCategoryHandler
{
    public function __construct(
        private CategoryService $categoryService,
    ) {}

    public function handle(int $id): void
    {
        $this->categoryService->deleteCategory($id);
    }
}
