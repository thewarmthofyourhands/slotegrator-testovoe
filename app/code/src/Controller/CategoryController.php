<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\UseCase\Category\AddCategoryDto;
use App\Dto\UseCase\Category\CategoryDto;
use App\Dto\UseCase\Category\UpdateCategoryDto;
use App\Infrastructure\Rest\ApiResponse;
use App\UseCase\Category\AddCategoryHandler;
use App\UseCase\Category\DeleteCategoryHandler;
use App\UseCase\Category\GetCategoryHandler;
use App\UseCase\Category\GetCategoryListHandler;
use App\UseCase\Category\UpdateCategoryHandler;
use App\Validation\Schema\Rest\Request\Category\EditCategorySchema;
use App\Validation\Schema\Rest\Request\Category\StoreCategorySchema;
use App\Validation\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
    public function __construct(
        private readonly Validator $validator,
        private readonly AddCategoryHandler $addCategoryHandler,
        private readonly GetCategoryHandler $getCategoryHandler,
        private readonly GetCategoryListHandler $getCategoryListHandler,
        private readonly UpdateCategoryHandler $updateCategoryHandler,
        private readonly DeleteCategoryHandler $deleteCategoryHandler,
    ) {}

    public function store(Request $request): Response
    {
        $category = json_decode($request->getContent());
        $this->validator->validate($category, StoreCategorySchema::SCHEMA);
        $id = $this->addCategoryHandler->handle(new AddCategoryDto(
            $category->title,
            $category->eId,
        ));

        return (new ApiResponse(compact('id'), 20100))->build(201);
    }

    public function index(Request $request): Response
    {
        $categoryDtoList = $this->getCategoryListHandler->handle();
        $categoryDataList = array_map(static fn(CategoryDto $dto) => $dto->toArray(), $categoryDtoList);

        return (new ApiResponse($categoryDataList))->build();
    }

    public function show(Request $request, int $id): Response
    {
        $categoryDto = $this->getCategoryHandler->handle($id);
        $categoryData = $categoryDto->toArray();

        return (new ApiResponse($categoryData))->build();
    }

    public function edit(Request $request, int $id): Response
    {
        $category = json_decode($request->getContent());
        $category->id = $id;
        $this->validator->validate($category, EditCategorySchema::SCHEMA);
        $this->updateCategoryHandler->handle(new UpdateCategoryDto(
            $category->id,
            $category->title,
            $category->eId,
        ));

        return (new ApiResponse())->build();
    }

    public function delete(Request $request, int $id): Response
    {
        $this->deleteCategoryHandler->handle($id);

        return (new ApiResponse())->build();
    }
}
