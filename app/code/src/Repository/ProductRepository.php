<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Repositories\ProductRepository\AddProductDto;
use App\Dto\Repositories\ProductRepository\ProductDto;
use App\Dto\Repositories\ProductRepository\UpdateProductDto;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

readonly class ProductRepository
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function getProductList(): array
    {
        $stmt = $this->connection->executeQuery(
            'select * from Products',
        );
        $productList = $stmt->fetchAllAssociative();
        $stmt->free();

        return array_map(
            static fn(array $product) => new ProductDto(
                $product['id'],
                $product['title'],
                (float) $product['price'],
                $product['eId'],
            ),
            $productList,
        );
    }

    public function findProductById(int $id): null|ProductDto
    {
        $stmt = $this->connection->executeQuery(
            'select * from Products where id = :id',
            [
                'id' => $id,
            ],
            [
                'id' => ParameterType::INTEGER
            ],
        );
        $product = $stmt->fetchAssociative();
        $stmt->free();

        return $product ? new ProductDto(
            $product['id'],
            $product['title'],
            (float) $product['price'],
            $product['eId'],
        ) : null;
    }

    public function addProduct(AddProductDto $addProductDto): int
    {
        $this->connection->insert(
            'Products',
            $addProductDto->toArray(),
            [
                'eId' => ParameterType::INTEGER,
                'price' => ParameterType::STRING,
            ]
        );

        return (int) $this->connection->lastInsertId();
    }

    public function updateProduct(UpdateProductDto $updateProductDto): void
    {
        $updateProductData = $updateProductDto->toArray();
        unset($updateProductData['id']);
        $this->connection->update(
            'Products',
            $updateProductData,
            [
                'id' => $updateProductDto->getId(),
            ],
            [
                'id' => ParameterType::INTEGER,
                'eId' => ParameterType::INTEGER,
                'price' => ParameterType::STRING,
            ],
        );
    }

    public function deleteProduct(int $id): void
    {
        $this->connection->delete(
            'Products',
            [
                'id' => $id,
            ],
        );
    }
}
