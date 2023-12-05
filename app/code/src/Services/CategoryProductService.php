<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\Services\CategoryService\CategoryDto;
use App\Repository\CategoryProductRepository;

readonly class CategoryProductService
{
    public function __construct(
        private CategoryProductRepository $categoryProductRepository,
    ) {}

    public function bindCategoryToProduct(int $categoryEId, int $productId): void
    {
        $this->categoryProductRepository->bindCategoryToProduct($categoryEId, $productId);
    }

    public function unbindAllCategoryToProduct(int $productId): void
    {
        $this->categoryProductRepository->unbindAllCategoryToProduct($productId);
    }

    public function getCategoryListByProductId(int $productId): array
    {
        $repositoryCategoryDtoList = $this->categoryProductRepository->getCategoryListByProductId($productId);

        return array_map(
            static fn(\App\Dto\Repositories\CategoryRepository\CategoryDto $repositoryCategory) => new CategoryDto(
                $repositoryCategory->getId(),
                $repositoryCategory->getTitle(),
                $repositoryCategory->getEId(),
            ),
            $repositoryCategoryDtoList,
        );
    }
}
