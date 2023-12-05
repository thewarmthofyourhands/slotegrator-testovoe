<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\Services\CategoryService\AddCategoryDto;
use App\Dto\Services\CategoryService\CategoryDto;
use App\Dto\Services\CategoryService\UpdateCategoryDto;
use App\Exceptions\Services\EntityNotFoundException;
use App\Repository\CategoryRepository;

readonly class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository
    ) {}

    public function getCategoryList(): array
    {
        $categoryDtoList = $this->categoryRepository->getCategoryList();

        return array_map(
            static fn(\App\Dto\Repositories\CategoryRepository\CategoryDto $categoryDto) => new CategoryDto(
                $categoryDto->getId(),
                $categoryDto->getTitle(),
                $categoryDto->getEId(),
            ),
            $categoryDtoList,
        );
    }

    public function getCategoryById(int $id): CategoryDto
    {
        $categoryDto = $this->categoryRepository->findCategoryById($id);

        if (null === $categoryDto) {
            throw new EntityNotFoundException();
        }

        return new CategoryDto(
            $categoryDto->getId(),
            $categoryDto->getTitle(),
            $categoryDto->getEId(),
        );
    }

    public function addCategory(AddCategoryDto $dto): int
    {
        return $this->categoryRepository->addCategory(
            new \App\Dto\Repositories\CategoryRepository\AddCategoryDto(
                $dto->getTitle(),
                $dto->getEId(),
            )
        );
    }

    public function updateCategory(UpdateCategoryDto $dto): void
    {
        $this->categoryRepository->updateCategory(
            new \App\Dto\Repositories\CategoryRepository\UpdateCategoryDto(
                $dto->getId(),
                $dto->getTitle(),
                $dto->getEId(),
            )
        );
    }

    public function deleteCategory(int $id): void
    {
        $this->categoryRepository->deleteCategory($id);
    }

    public function dtoToText(CategoryDto $categoryDto): string
    {
        return <<<EOL
        id: {$categoryDto->getId()}
        eId: {$categoryDto->getEId()}
        title: {$categoryDto->getTitle()}
        EOL;
    }
}
