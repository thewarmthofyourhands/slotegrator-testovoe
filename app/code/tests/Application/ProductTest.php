<?php

declare(strict_types=1);

namespace Tests\Application;

use App\Exceptions\Application\ApplicationErrorCodeEnum;
use App\Exceptions\Application\ApplicationErrorMessagesEnum;
use App\Exceptions\Validation\ValidatorException;
use App\Validation\Schema\Rest\Response\Product\ProductListSchema;
use App\Validation\Schema\Rest\Response\Product\ProductSchema;
use App\Validation\Validator;
use Eva\Http\Client;
use Eva\Http\HttpMethodsEnum;
use Eva\Http\Message\Request;
use JsonException;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function testProductAdd(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/products', [],
            json_encode([
                'title' => 'Product 1',
                'price' => 80.00,
                'eId' => 124124125,
                'categoriesEId' => [1245125, 626263],
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(20100, $responseData['code']);
        $this->assertEquals(true, is_int($responseData['data']['id']));
        $notifyData = file_get_contents('var/notifications/messages.txt');
        $this->assertEquals(
            <<<EOL
            id: {$responseData['data']['id']}
            eId: 124124125
            title: Product 1
            price: 80
            categoryEIdList: 
            EOL,
            $notifyData
        );
    }

    /**
     * @throws JsonException
     */
    public function testProductAddWithoutEId(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/products', [],
            json_encode([
                'title' => 'Product 1',
                'price' => 101.55,
                'eId' => null,
                'categoriesEId' => [1245125, 626263],
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(20100, $responseData['code']);
        $this->assertEquals(true, is_int($responseData['data']['id']));
    }

    /**
     * @throws JsonException
     */
    public function testProductAddWithoutEIdAndCategories(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/products', [],
            json_encode([
                'title' => 'Product 1',
                'price' => 101.55,
                'eId' => null,
                'categoriesEId' => [],
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(20100, $responseData['code']);
        $this->assertEquals(true, is_int($responseData['data']['id']));
    }

    /**
     * @throws JsonException
     */
    public function testProductAddWithWrongTitle(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/products', [],
            json_encode([
                'title' => 'Pr',
                'price' => 101.55,
                'eId' => 124124125,
                'categoriesEId' => [1245125, 626263],
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(42200, $responseData['code']);
        $this->assertEquals(<<<EOL
        Validation errors:
        [title] - Must be at least 3 characters long 
        
        EOL, $responseData['message']);

        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/products', [],
            json_encode([
                'title' => 'Product 12312412521521521',
                'price' => 101.55,
                'eId' => 124124125,
                'categoriesEId' => [1245125, 626263],
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(42200, $responseData['code']);
        $this->assertEquals(<<<EOL
        Validation errors:
        [title] - Must be at most 12 characters long 
        
        EOL, $responseData['message']);
    }

    /**
     * @throws JsonException
     */
    public function testProductAddWithWrongPrice(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/products', [],
            json_encode([
                'title' => 'Product 1',
                'price' => 101.555,
                'eId' => 124124125,
                'categoriesEId' => [1245125, 626263],
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(42200, $responseData['code']);
        $this->assertEquals(<<<EOL
        Validation errors:
        [price] - Must be a multiple of 0.01 
        
        EOL, $responseData['message']);

        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/products', [],
            json_encode([
                'title' => 'Product 1',
                'price' => 300,
                'eId' => 124124125,
                'categoriesEId' => [1245125, 626263],
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(42200, $responseData['code']);
        $this->assertEquals(<<<EOL
        Validation errors:
        [price] - Must have a maximum value of 200 
        
        EOL, $responseData['message']);
    }

    /**
     * @throws JsonException
     */
    public function testProductIndex(): void
    {
        $request = new Request(HttpMethodsEnum::GET, 'http://127.0.0.1/api/products');
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(20000, $responseData['code']);
        $validator = new Validator();
        $validator->validate($responseData['data'], ProductListSchema::SCHEMA);
    }

    /**
     * @throws JsonException|ValidatorException
     */
    public function testProductShow(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/categories', [],
            json_encode([
                'title' => 'Category 2',
                'eId' => 100101,
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $categoryId = $responseData['data']['id'];

        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/products', [],
            json_encode([
                'title' => 'Product 2',
                'price' => 85.98,
                'eId' => 124124125,
                'categoriesEId' => [100101, 626263],
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $id = $responseData['data']['id'];

        $request = new Request(HttpMethodsEnum::GET, 'http://127.0.0.1/api/products/'.$id);
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(20000, $responseData['code']);
        $validator = new Validator();
        $validator->validate($responseData['data'], ProductSchema::SCHEMA);
        $this->assertEquals($id, $responseData['data']['id']);
        $this->assertEquals('Product 2', $responseData['data']['title']);
        $this->assertEquals(85.98, $responseData['data']['price']);
        $this->assertEquals(124124125, $responseData['data']['eId']);
        $this->assertEquals(1, count($responseData['data']['categoryList']));
        $this->assertEquals($categoryId, $responseData['data']['categoryList'][0]['id']);
        $this->assertEquals('Category 2', $responseData['data']['categoryList'][0]['title']);

        $request = new Request(HttpMethodsEnum::DELETE, 'http://127.0.0.1/api/categories/'.$categoryId);
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(20000, $responseData['code']);
    }

    /**
     * @throws JsonException|ValidatorException
     */
    public function testProductUpdate(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/categories', [],
            json_encode([
                'title' => 'Category 3',
                'eId' => 100102,
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $categoryId = $responseData['data']['id'];

        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/products', [],
            json_encode([
                'title' => 'Product 3',
                'price' => 125.13,
                'eId' => null,
                'categoriesEId' => [],
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $id = $responseData['data']['id'];

        $request = new Request(HttpMethodsEnum::PUT, 'http://127.0.0.1/api/products/'.$id, [],
            json_encode([
                'title' => 'Product 0',
                'price' => 80.00,
                'eId' => 3303,
                'categoriesEId' => [100102, 626263],
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(20000, $responseData['code']);

        $request = new Request(HttpMethodsEnum::GET, 'http://127.0.0.1/api/products/'.$id);
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(20000, $responseData['code']);
        $validator = new Validator();
        $validator->validate($responseData['data'], ProductSchema::SCHEMA);
        $this->assertEquals($id, $responseData['data']['id']);
        $this->assertEquals('Product 0', $responseData['data']['title']);
        $this->assertEquals(80.00, $responseData['data']['price']);
        $this->assertEquals(3303, $responseData['data']['eId']);
        $this->assertEquals(1, count($responseData['data']['categoryList']));

        $request = new Request(HttpMethodsEnum::DELETE, 'http://127.0.0.1/api/categories/'.$categoryId);
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(20000, $responseData['code']);

        $notifyData = file_get_contents('var/notifications/messages.txt');
        $this->assertEquals(
            <<<EOL
            id: {$id}
            eId: 3303
            title: Product 0
            price: 80
            categoryEIdList: 100102
            EOL,
            $notifyData
        );
    }

    /**
     * @throws JsonException
     */
    public function testProductDelete(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/categories', [],
            json_encode([
                'title' => 'Category 2',
                'eId' => 100101,
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $categoryId = $responseData['data']['id'];

        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/products', [],
            json_encode([
                'title' => 'Product 2',
                'price' => 85.98,
                'eId' => 124124125,
                'categoriesEId' => [100101, 626263],
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $id = $responseData['data']['id'];

        $request = new Request(HttpMethodsEnum::DELETE, 'http://127.0.0.1/api/categories/'.$categoryId);
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(20000, $responseData['code']);

        $request = new Request(HttpMethodsEnum::DELETE, 'http://127.0.0.1/api/products/'.$id);
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(20000, $responseData['code']);

        $request = new Request(HttpMethodsEnum::GET, 'http://127.0.0.1/api/products/'.$id);
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(ApplicationErrorCodeEnum::PRODUCT_NOT_FOUND->value, $responseData['code']);
        $this->assertEquals(ApplicationErrorMessagesEnum::PRODUCT_NOT_FOUND->value, $responseData['message']);
    }
}
