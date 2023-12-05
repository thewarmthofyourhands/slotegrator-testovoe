<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Dto\Services\CategoryService\CategoryDto;
use App\Dto\UseCase\Product\ProductDto;
use App\Services\ProductService;

readonly class GetProductListHandler
{
    public function __construct(
        private ProductService $productService,
    ) {}

    public function handle(): array
    {
        $serviceProductDtoList = $this->productService->getProductList();
        $productDtoList = array_map(
            static fn(\App\Dto\Services\ProductService\ProductDto $serviceProductDto) => new ProductDto(
                $serviceProductDto->getId(),
                $serviceProductDto->getTitle(),
                $serviceProductDto->getPrice(),
                $serviceProductDto->getEId(),
                array_map(
                    static fn(CategoryDto $dto) => $dto->toArray(),
                    $serviceProductDto->getCategoryList(),
                ),
            ),
            $serviceProductDtoList,
        );

        return $productDtoList;
    }
}
