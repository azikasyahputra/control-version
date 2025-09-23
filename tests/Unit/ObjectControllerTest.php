<?php

namespace Tests\Unit\Http\Controllers;

use App\Data\Object\GetObjectData;
use App\Data\Object\StoreObjectData;
use App\Models\Objects;
use App\Services\ObjectServices;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ObjectControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $objectServicesMock;

    public function setUp(): void
    {
        parent::setUp();
        // Create a mock for the ObjectServices class
        $this->objectServicesMock = Mockery::mock(ObjectServices::class);
        $this->app->instance(ObjectServices::class, $this->objectServicesMock);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function index_returns_all_version_data_successfully()
    {
        // Arrange
        $expectedData = new Collection([
            ['key' => 'test1', 'value' => 'value1'],
            ['key' => 'test2', 'value' => 'value2'],
        ]);

        $this->objectServicesMock
            ->shouldReceive('all')
            ->once()
            ->andReturn($expectedData);

        // Act
        $response = $this->getJson('/api/object/get_all_records');

        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedData->toArray());
    }

    /**
     * @test
     */
    public function index_returns_404_when_no_data_is_found()
    {
        // Arrange
        $this->objectServicesMock
            ->shouldReceive('all')
            ->once()
            ->andReturn(new Collection());

        // Act
        $response = $this->getJson('/api/object/get_all_records');
        // Assert
        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'errors' => [
                    'message' => 'Data Not Found'
                ]
            ]);
    }

    /**
     * @test
     */
    public function store_creates_new_object_successfully()
    {
        // Arrange
        $requestData = ['mykey' => 'myvalue'];
        // The service returns a timestamp array
        $expectedResponse = 'Time: 10:30 AM';

        $this->objectServicesMock
            ->shouldReceive('store')
            ->once()
             // Assert that the service is called with the correct DTO
            ->with(Mockery::on(function ($dto) {
                return $dto instanceof StoreObjectData &&
                       $dto->key === 'mykey' &&
                       $dto->value === 'myvalue'; // Corrected: The value should be a plain string.
            }))
            ->andReturn($expectedResponse);

        // Act
        $response = $this->postJson('/api/object', $requestData);

        // Assert
        $response->assertStatus(201);
        
        $responseBody = json_decode($response->getContent());

        $this->assertEquals($expectedResponse,$responseBody);
    }

    /**
     * @test
     */
    public function store_returns_validation_error_for_invalid_data()
    {
        // Arrange: More than one key in the request body
        $requestData = ['key1' => 'value1', 'key2' => 'value2'];

        // Act
        $response = $this->postJson('/api/object', $requestData);

        // Assert
        $response->assertStatus(422) // Unprocessable Entity for validation errors
                 ->assertJsonValidationErrors('body');
    }

    /**
     * @test
     */
    public function show_returns_specific_object_data_successfully()
    {
        // Arrange
        $key = 'mykey';
        // The service is expected to return a Objects model instance
        $objectModel = new Objects(['key' => $key, 'value' => 'myvalue']);
        $expectedJson = ['key' => $key, 'value' => 'myvalue'];

        $this->objectServicesMock
            ->shouldReceive('find')
            ->once()
            // Assert that the service is called with the correct DTO
            ->with(Mockery::on(function ($dto) use ($key) {
                return $dto instanceof GetObjectData && $dto->key === $key && $dto->timestamp === null;
            }))
            ->andReturn($objectModel);
        
        // Act
        $response = $this->getJson("/api/object/{$key}");

        // Assert
        $response->assertStatus(200)
                 ->assertJsonFragment($expectedJson);
    }
    
    /**
     * @test
     */
    public function show_returns_specific_object_data_with_timestamp_successfully()
    {
        // Arrange
        $key = 'mykey';
        $timestamp = 1672531200; // Example: 2023-01-01 00:00:00
        $objectModel = new Objects(['key' => $key, 'value' => 'old-value']);
        $expectedJson = ['key' => $key, 'value' => 'old-value'];

        $this->objectServicesMock
            ->shouldReceive('find')
            ->once()
            // Assert that the service is called with the correct DTO
            ->with(Mockery::on(function ($dto) use ($key, $timestamp) {
                return $dto instanceof GetObjectData && $dto->key === $key && $dto->timestamp === $timestamp;
            }))
            ->andReturn($objectModel);

        // Act
        $response = $this->getJson("/api/object/{$key}?timestamp={$timestamp}");

        // Assert
        $response->assertStatus(200)
                 ->assertJsonFragment($expectedJson);
    }


    /**
     * @test
     */
    public function show_returns_404_when_specific_object_data_not_found()
    {
        // Arrange
        $key = 'nonexistent-key';
        $this->objectServicesMock
            ->shouldReceive('find')
            ->once()
            ->andReturn(null);

        // Act
        $response = $this->getJson("/api/object/{$key}");

        // Assert
        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'errors' => [
                    'message' => 'Data Not Found'
                ]
            ]);
    }
}