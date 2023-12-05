<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\UseCase\Product\AddProductDto;
use App\Dto\UseCase\Product\ProductDto;
use App\Dto\UseCase\Product\UpdateProductDto;
use App\Infrastructure\Rest\ApiResponse;
use App\UseCase\Product\AddProductHandler;
use App\UseCase\Product\DeleteProductHandler;
use App\UseCase\Product\GetProductHandler;
use App\UseCase\Product\GetProductListHandler;
use App\UseCase\Product\UpdateProductHandler;
use App\Validation\Schema\Rest\Request\Product\EditProductSchema;
use App\Validation\Schema\Rest\Request\Product\StoreProductSchema;
use App\Validation\Schema\Rest\Response\Product\ProductSchema;
use App\Validation\Schema\Rest\Response\Product\ProductListSchema;
use App\Validation\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly Validator $validator,
        private readonly AddProductHandler $addProductHandler,
        private readonly GetProductHandler $getProductHandler,
        private readonly GetProductListHandler $getProductListHandler,
        private readonly UpdateProductHandler $updateProductHandler,
        private readonly DeleteProductHandler $deleteProductHandler,
    ) {}

    public function store(Request $request): Response
    {
        $product = json_decode($request->getContent());
        $this->validator->validate($product, StoreProductSchema::SCHEMA);
        $id = $this->addProductHandler->handle(new AddProductDto(
            $product->title,
            $product->price,
            $product->eId,
            $product->categoriesEId,
        ));

        return (new ApiResponse(compact('id'), 20100))->build(201);
    }

    public function index(Request $request): Response
    {
        $productDtoList = $this->getProductListHandler->handle();
        $productDataList = array_map(static fn(ProductDto $dto) => $dto->toArray(), $productDtoList);

        return (new ApiResponse($productDataList))->build();
    }

    public function show(Request $request, int $id): Response
    {
        $productDto = $this->getProductHandler->handle($id);
        $productData = $productDto->toArray();

        return (new ApiResponse($productData))->build();
    }

    public function edit(Request $request, int $id): Response
    {
        $product = json_decode($request->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $product->id = $id;
        $this->validator->validate($product, EditProductSchema::SCHEMA);
        $this->updateProductHandler->handle(new UpdateProductDto(
            $product->id,
            $product->title,
            $product->price,
            $product->eId,
            $product->categoriesEId,
        ));

        return (new ApiResponse())->build();
    }

    public function delete(Request $request, int $id): Response
    {
        $this->deleteProductHandler->handle($id);

        return (new ApiResponse())->build();
    }
}
