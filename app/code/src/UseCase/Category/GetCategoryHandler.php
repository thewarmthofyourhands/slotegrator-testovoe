<?php

declare(strict_types=1);

namespace App\UseCase\Category;

use App\Dto\UseCase\Category\CategoryDto;
use App\Exceptions\Application\ApplicationErrorCodeEnum;
use App\Exceptions\Application\ApplicationException;
use App\Exceptions\Services\EntityNotFoundException;
use App\Services\CategoryService;

readonly class GetCategoryHandler
{
    public function __construct(
        private CategoryService $categoryService,
    ) {}

    public function handle(int $id): CategoryDto
    {

        try {
            $serviceCategoryDto = $this->categoryService->getCategoryById($id);
        } catch (EntityNotFoundException $entityNotFoundException) {
            throw new ApplicationException(ApplicationErrorCodeEnum::CATEGORY_NOT_FOUND);
        }

        return new CategoryDto(
            $serviceCategoryDto->getId(),
            $serviceCategoryDto->getTitle(),
            $serviceCategoryDto->getEId(),
        );
    }
}
