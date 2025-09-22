<?php

namespace Tests\Unit\Traits;

use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class ResponseTraitTest extends TestCase
{
    private $traitObject;

    public function setUp(): void
    {
        parent::setUp();
        // Create an anonymous class that uses the trait for isolated testing
        $this->traitObject = new class {
            use ResponseTrait;
        };
    }

    /** @test */
    public function success_method_returns_a_valid_json_response()
    {
        // Arrange
        $data = ['message' => 'Success'];
        $statusCode = 201;

        // Act
        $response = $this->traitObject->success($data, $statusCode);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($data, $response->getData(true));
    }
    
    /** @test */
    public function success_method_uses_default_status_code_of_200()
    {
        // Arrange
        $data = ['data' => 'some data'];

        // Act
        $response = $this->traitObject->success($data);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function error_method_returns_a_valid_json_response()
    {
        // Arrange
        $errors = ['field' => ['The field is required.']];
        $statusCode = 422;
        $expectedJson = [
            'status' => 'error',
            'errors' => $errors,
        ];

        // Act
        $response = $this->traitObject->error($errors, $statusCode);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($expectedJson, $response->getData(true));
    }
}
