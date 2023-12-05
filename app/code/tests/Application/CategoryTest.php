<?php

declare(strict_types=1);

namespace Tests\Application;

use App\Exceptions\Application\ApplicationErrorCodeEnum;
use App\Exceptions\Application\ApplicationErrorMessagesEnum;
use App\Validation\Schema\Rest\Response\Category\CategoryListSchema;
use App\Validation\Schema\Rest\Response\Category\CategorySchema;
use App\Validation\Validator;
use Eva\Http\Client;
use Eva\Http\HttpMethodsEnum;
use Eva\Http\Message\Request;
use JsonException;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function testCategoryAdd(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/categories', [],
            json_encode([
                'title' => 'Category 1',
                'eId' => 124124125,
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
            title: Category 1
            EOL,
            $notifyData
        );
    }

    /**
     * @throws JsonException
     */
    public function testCategoryAddWithoutEId(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/categories', [],
            json_encode([
                'title' => 'Category 1',
                'eId' => null
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
    public function testCategoryAddWithWrongTitle(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/categories', [],
            json_encode([
                'title' => 'Category 333333',
                'eId' => null,
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

        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/categories', [],
            json_encode([
                'title' => 'Ca',
                'eId' => null,
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
    }

    /**
     * @throws JsonException
     */
    public function testCategoryIndex(): void
    {
        $request = new Request(
            HttpMethodsEnum::GET,
            'http://127.0.0.1/api/categories',
        );
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $validator = new Validator();
        $validator->validate($responseData['data'], CategoryListSchema::SCHEMA);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(20000, $responseData['code']);
    }

    /**
     * @throws JsonException
     */
    public function testCategoryShow(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/categories', [],
            json_encode([
                'title' => 'Category 1',
                'eId' => null
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $id = $responseData['data']['id'];
        $request = new Request(
            HttpMethodsEnum::GET,
            'http://127.0.0.1/api/categories/'.$id,
        );
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(20000, $responseData['code']);
        $validator = new Validator();
        $validator->validate($responseData['data'], CategorySchema::SCHEMA);
    }

    /**
     * @throws JsonException
     */
    public function testCategoryUpdate(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/categories', [],
            json_encode([
                'title' => 'Category 1',
                'eId' => null
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $id = $responseData['data']['id'];

        $request = new Request(HttpMethodsEnum::PUT, 'http://127.0.0.1/api/categories/'.$id, [],
            json_encode([
                'title' => 'Category 1',
                'eId' => 124412
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(20000, $responseData['code']);

        $request = new Request(
            HttpMethodsEnum::GET,
            'http://127.0.0.1/api/categories/'.$id,
        );
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(20000, $responseData['code']);
        $validator = new Validator();
        $validator->validate($responseData['data'], CategorySchema::SCHEMA);
        $this->assertEquals(124412, $responseData['data']['eId']);

        $notifyData = file_get_contents('var/notifications/messages.txt');
        $this->assertEquals(
            <<<EOL
            id: {$id}
            eId: 124412
            title: Category 1
            EOL,
            $notifyData
        );
    }

    /**
     * @throws JsonException
     */
    public function testCategoryUpdateWithWrongTitle(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/categories', [],
            json_encode([
                'title' => 'Category 1',
                'eId' => null
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $id = $responseData['data']['id'];

        $request = new Request(HttpMethodsEnum::PUT, 'http://127.0.0.1/api/categories/'.$id, [],
            json_encode([
                'title' => 'Ca',
                'eId' => null,
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

        $request = new Request(HttpMethodsEnum::PUT, 'http://127.0.0.1/api/categories/'.$id, [],
            json_encode([
                'title' => 'Category 11125125125125215',
                'eId' => null,
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
    public function testCategoryDelete(): void
    {
        $request = new Request(HttpMethodsEnum::POST, 'http://127.0.0.1/api/categories', [],
            json_encode([
                'title' => 'Category 1',
                'eId' => null
            ], JSON_THROW_ON_ERROR),
        );
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $id = $responseData['data']['id'];

        $request = new Request(HttpMethodsEnum::DELETE, 'http://127.0.0.1/api/categories/'.$id);
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(20000, $responseData['code']);

        $request = new Request(HttpMethodsEnum::GET, 'http://127.0.0.1/api/categories/'.$id);
        $response = (new Client())->sendRequest($request);
        $responseData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(ApplicationErrorCodeEnum::CATEGORY_NOT_FOUND->value, $responseData['code']);
        $this->assertEquals(ApplicationErrorMessagesEnum::CATEGORY_NOT_FOUND->value, $responseData['message']);
    }
}
