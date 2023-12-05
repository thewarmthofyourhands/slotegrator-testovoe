<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Services\ProductService;

readonly class DeleteProductHandler
{
    public function __construct(
        private ProductService $productService,
    ) {}

    public function handle(int $id): void
    {
        $this->productService->deleteProduct($id);
    }
}
