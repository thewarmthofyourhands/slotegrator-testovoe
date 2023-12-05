<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\Services\CategoryService\CategoryDto;
use App\Dto\Services\ProductService\AddProductDto;
use App\Dto\Services\ProductService\ProductDto;
use App\Dto\Services\ProductService\UpdateProductDto;
use App\Exceptions\Services\EntityNotFoundException;
use App\Repository\ProductRepository;

readonly class ProductService
{
    public function __construct(
        private ProductRepository $productRepository,
        private CategoryProductService $categoryProductService,
        private TransactionService $transactionService,
    ) {}

    public function addProduct(AddProductDto $dto): int
    {
        $this->transactionService->beginTransaction();

        try {
            $id = $this->productRepository->addProduct(
                new \App\Dto\Repositories\ProductRepository\AddProductDto(
                    $dto->getTitle(),
                    $dto->getPrice(),
                    $dto->getEId(),
                ),
            );

            foreach ($dto->getCategoryEIdList() as $categoryEId) {
                $this->categoryProductService->bindCategoryToProduct(
                    $categoryEId,
                    $id,
                );
            }

            $this->transactionService->commit();
        } catch (\Throwable $e) {
            $this->transactionService->rollback();

            throw $e;
        }

        return $id;
    }

    public function getProductById(int $id): ProductDto
    {
        $productDto = $this->productRepository->findProductById($id);

        if (null === $productDto) {
            throw new EntityNotFoundException();
        }

        $categoryDtoList = $this->categoryProductService->getCategoryListByProductId($productDto->getId());

        return new ProductDto(
            $productDto->getId(),
            $productDto->getTitle(),
            $productDto->getPrice(),
            $productDto->getEId(),
            $categoryDtoList,
        );
    }

    public function getProductList(): array
    {
        $repositoryProductDtoList = $this->productRepository->getProductList();
        $productDtoList = [];

        foreach ($repositoryProductDtoList as $repositoryProductDto) {
            assert($repositoryProductDto instanceof \App\Dto\Repositories\ProductRepository\ProductDto);
            $categoryDtoList = $this->categoryProductService->getCategoryListByProductId($repositoryProductDto->getId());
            $productDtoList[] = new ProductDto(
                $repositoryProductDto->getId(),
                $repositoryProductDto->getTitle(),
                $repositoryProductDto->getPrice(),
                $repositoryProductDto->getEId(),
                $categoryDtoList,
            );
        }

        return $productDtoList;
    }

    public function updateProduct(UpdateProductDto $updateProductDto): void
    {
        $this->transactionService->beginTransaction();

        try {
            $this->productRepository->updateProduct(
                new \App\Dto\Repositories\ProductRepository\UpdateProductDto(
                    $updateProductDto->getId(),
                    $updateProductDto->getTitle(),
                    $updateProductDto->getPrice(),
                    $updateProductDto->getEId(),
                ),
            );
            $this->categoryProductService->unbindAllCategoryToProduct($updateProductDto->getId());

            foreach ($updateProductDto->getCategoryEIdList() as $categoryEId) {
                $this->categoryProductService->bindCategoryToProduct(
                    $categoryEId,
                    $updateProductDto->getId(),
                );
            }

            $this->transactionService->commit();
        } catch (\Throwable $e) {
            $this->transactionService->rollback();
            throw $e;
        }
    }

    public function deleteProduct(int $id): void
    {
        $this->productRepository->deleteProduct($id);
    }

    public function dtoToText(ProductDto $productDto): string
    {
        $categoryEIdList = array_map(
            static fn(CategoryDto $dto) => $dto->getEId(),
            $productDto->getCategoryList(),
        );
        $categoryEIdListString = implode(',', $categoryEIdList);

        return <<<EOL
        id: {$productDto->getId()}
        eId: {$productDto->getEId()}
        title: {$productDto->getTitle()}
        price: {$productDto->getPrice()}
        categoryEIdList: {$categoryEIdListString}
        EOL;
    }
}
