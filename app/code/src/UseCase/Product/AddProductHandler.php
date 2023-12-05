<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Dto\UseCase\Product\AddProductDto;
use App\Services\Notifications\NotificationServiceInterface;
use App\Services\ProductService;

readonly class AddProductHandler
{
    public function __construct(
        private ProductService $productService,
        private NotificationServiceInterface $notificationService,
    ) {}

    public function handle(AddProductDto $dto): int
    {
        $id = $this->productService->addProduct(new \App\Dto\Services\ProductService\AddProductDto(
            $dto->getTitle(),
            $dto->getPrice(),
            $dto->getEId(),
            $dto->getCategoryEIdList(),
        ));
        $productDto = $this->productService->getProductById($id);
        $this->notificationService->notify(
            $this->productService->dtoToText($productDto),
        );

        return $id;
    }
}
