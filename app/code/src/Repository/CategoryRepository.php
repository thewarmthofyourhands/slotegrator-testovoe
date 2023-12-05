<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Repositories\CategoryRepository\AddCategoryDto;
use App\Dto\Repositories\CategoryRepository\CategoryDto;
use App\Dto\Repositories\CategoryRepository\UpdateCategoryDto;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

readonly class CategoryRepository
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function findCategoryById(int $id): null|CategoryDto
    {
        $stmt = $this->connection->executeQuery(
            'select * from Categories where id = :id',
            [
                'id' => $id,
            ],
            [
                'id' => ParameterType::INTEGER
            ],
        );

        $category = $stmt->fetchAssociative();
        $stmt->free();

        return $category ? new CategoryDto(...$category) : null;
    }

    public function getCategoryList(): array
    {
        $stmt = $this->connection->executeQuery(
            'select * from Categories',
        );
        $categoryList = $stmt->fetchAllAssociative();
        $stmt->free();

        return array_map(
            static fn(array $category) => new CategoryDto(...$category),
            $categoryList,
        );
    }

    public function addCategory(AddCategoryDto $addCategoryDto): int
    {
        $this->connection->insert(
            'Categories',
            $addCategoryDto->toArray(),
            [
                'eId' => ParameterType::INTEGER,
            ]
        );

        return (int) $this->connection->lastInsertId();
    }

    public function updateCategory(UpdateCategoryDto $updateCategoryDto): void
    {
        $updateCategoryData = $updateCategoryDto->toArray();
        unset($updateCategoryData['id']);
        $this->connection->update(
            'Categories',
            $updateCategoryData,
            [
                'id' => $updateCategoryDto->getId(),
            ],
            [
                'id' => ParameterType::INTEGER,
                'eId' => ParameterType::INTEGER,
            ],
        );
    }

    public function deleteCategory(int $id): void
    {
        $this->connection->delete(
            'Categories',
            [
                'id' => $id,
            ],
            [
                'id' => ParameterType::INTEGER,
            ],
        );
    }
}
