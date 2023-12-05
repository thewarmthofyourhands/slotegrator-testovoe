<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Repositories\CategoryRepository\CategoryDto;
use Doctrine\DBAL\Connection;

readonly class CategoryProductRepository
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function bindCategoryToProduct(int $categoryEId, int $productId): void
    {
        $this->connection->insert('CategoryProduct', [
            'categoryEId' => $categoryEId,
            'productId' => $productId,
        ]);
    }

    public function unbindAllCategoryToProduct(int $productId): void
    {
        $this->connection->delete('CategoryProduct', [
            'productId' => $productId,
        ]);
    }

    public function getCategoryListByProductId(int $productId): array
    {
        $stmt = $this->connection->executeQuery(
            <<<EOL
            select c.* from CategoryProduct as cp
            inner join Categories as c on c.eId = cp.categoryEId
            where cp.productId = :productId
            EOL,
            [
                'productId' => $productId,
            ]
        );
        $categoryList = $stmt->fetchAllAssociative();
        $stmt->free();

        return array_map(
            static fn(array $category) => new CategoryDto(...$category),
            $categoryList,
        );
    }
}
