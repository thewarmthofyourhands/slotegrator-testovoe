<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Dto\Services\CategoryService\CategoryDto;
use App\Dto\UseCase\Product\ProductDto;
use App\Exceptions\Application\ApplicationErrorCodeEnum;
use App\Exceptions\Application\ApplicationException;
use App\Exceptions\Services\EntityNotFoundException;
use App\Services\ProductService;

readonly class GetProductHandler
{
    public function __construct(
        private ProductService $productService,
    ) {}

    public function handle(int $id): ProductDto
    {
        try {
            $serviceProductDto = $this->productService->getProductById($id);
        } catch (EntityNotFoundException $entityNotFoundException) {
            throw new ApplicationException(ApplicationErrorCodeEnum::PRODUCT_NOT_FOUND);
        }

        return new ProductDto(
            $serviceProductDto->getId(),
            $serviceProductDto->getTitle(),
            $serviceProductDto->getPrice(),
            $serviceProductDto->getEId(),
            array_map(
                static fn(CategoryDto $dto) => $dto->toArray(),
                $serviceProductDto->getCategoryList(),
            ),
        );
    }
}
