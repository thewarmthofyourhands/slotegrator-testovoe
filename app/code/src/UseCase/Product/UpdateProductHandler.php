<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Dto\UseCase\Product\UpdateProductDto;
use App\Services\Notifications\NotificationServiceInterface;
use App\Services\ProductService;

readonly class UpdateProductHandler
{
    public function __construct(
        private ProductService $productService,
        private NotificationServiceInterface $notificationService,
    ) {}

    public function handle(UpdateProductDto $dto): void
    {
        $this->productService->updateProduct(new \App\Dto\Services\ProductService\UpdateProductDto(
            $dto->getId(),
            $dto->getTitle(),
            $dto->getPrice(),
            $dto->getEId(),
            $dto->getCategoryEIdList(),
        ));
        $productDto = $this->productService->getProductById($dto->getId());
        $this->notificationService->notify(
            $this->productService->dtoToText($productDto),
        );
    }
}
