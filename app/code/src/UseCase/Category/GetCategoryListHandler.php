<?php

declare(strict_types=1);

namespace App\UseCase\Category;

use App\Dto\UseCase\Category\CategoryDto;
use App\Services\CategoryService;

readonly class GetCategoryListHandler
{
    public function __construct(
        private CategoryService $categoryService,
    ) {}

    public function handle(): array
    {
        $serviceCategoryDtoList = $this->categoryService->getCategoryList();

        return array_map(
            static fn(\App\Dto\Services\CategoryService\CategoryDto $serviceCategoryDto) => new CategoryDto(
                $serviceCategoryDto->getId(),
                $serviceCategoryDto->getTitle(),
                $serviceCategoryDto->getEId(),
            ),
            $serviceCategoryDtoList,
        );
    }
}
