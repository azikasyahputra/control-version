<?php

namespace Tests\Unit\Http\Controllers;

use App\Data\Version\GetVersionData;
use App\Data\Version\StoreVersionData;
use App\Models\Version;
use App\Services\VersionServices;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class VersionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $versionServicesMock;

    public function setUp(): void
    {
        parent::setUp();
        // Create a mock for the VersionServices class
        $this->versionServicesMock = Mockery::mock(VersionServices::class);
        $this->app->instance(VersionServices::class, $this->versionServicesMock);
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

        $this->versionServicesMock
            ->shouldReceive('all')
            ->once()
            ->andReturn($expectedData);

        // Act
        $response = $this->getJson('/api/version/get_all_records');

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
        $this->versionServicesMock
            ->shouldReceive('all')
            ->once()
            ->andReturn(new Collection());

        // Act
        $response = $this->getJson('/api/version/get_all_records');
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
    public function store_creates_new_version_successfully()
    {
        // Arrange
        $requestData = ['mykey' => 'myvalue'];
        // The service returns a timestamp array
        $expectedResponse = ['Time' => '10:30 AM'];

        $this->versionServicesMock
            ->shouldReceive('store')
            ->once()
             // Assert that the service is called with the correct DTO
            ->with(Mockery::on(function ($dto) {
                return $dto instanceof StoreVersionData &&
                       $dto->key === 'mykey' &&
                       $dto->value === 'myvalue'; // Corrected: The value should be a plain string.
            }))
            ->andReturn($expectedResponse);

        // Act
        $response = $this->postJson('/api/version', $requestData);

        // Assert
        $response->assertStatus(201)
                 ->assertJson($expectedResponse);
    }

    /**
     * @test
     */
    public function store_returns_validation_error_for_invalid_data()
    {
        // Arrange: More than one key in the request body
        $requestData = ['key1' => 'value1', 'key2' => 'value2'];

        // Act
        $response = $this->postJson('/api/version', $requestData);

        // Assert
        $response->assertStatus(422) // Unprocessable Entity for validation errors
                 ->assertJsonValidationErrors('body');
    }

    /**
     * @test
     */
    public function show_returns_specific_version_data_successfully()
    {
        // Arrange
        $key = 'mykey';
        // The service is expected to return a Version model instance
        $versionModel = new Version(['key' => $key, 'value' => 'myvalue']);
        $expectedJson = ['key' => $key, 'value' => 'myvalue'];

        $this->versionServicesMock
            ->shouldReceive('find')
            ->once()
            // Assert that the service is called with the correct DTO
            ->with(Mockery::on(function ($dto) use ($key) {
                return $dto instanceof GetVersionData && $dto->key === $key && $dto->timestamp === null;
            }))
            ->andReturn($versionModel);
        
        // Act
        $response = $this->getJson("/api/version/{$key}");

        // Assert
        $response->assertStatus(200)
                 ->assertJsonFragment($expectedJson);
    }
    
    /**
     * @test
     */
    public function show_returns_specific_version_data_with_timestamp_successfully()
    {
        // Arrange
        $key = 'mykey';
        $timestamp = 1672531200; // Example: 2023-01-01 00:00:00
        $versionModel = new Version(['key' => $key, 'value' => 'old-value']);
        $expectedJson = ['key' => $key, 'value' => 'old-value'];

        $this->versionServicesMock
            ->shouldReceive('find')
            ->once()
            // Assert that the service is called with the correct DTO
            ->with(Mockery::on(function ($dto) use ($key, $timestamp) {
                return $dto instanceof GetVersionData && $dto->key === $key && $dto->timestamp === $timestamp;
            }))
            ->andReturn($versionModel);

        // Act
        $response = $this->getJson("/api/version/{$key}?timestamp={$timestamp}");

        // Assert
        $response->assertStatus(200)
                 ->assertJsonFragment($expectedJson);
    }


    /**
     * @test
     */
    public function show_returns_404_when_specific_version_data_not_found()
    {
        // Arrange
        $key = 'nonexistent-key';
        $this->versionServicesMock
            ->shouldReceive('find')
            ->once()
            ->andReturn(null);

        // Act
        $response = $this->getJson("/api/version/{$key}");

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