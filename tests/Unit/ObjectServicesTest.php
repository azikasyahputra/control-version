<?php

namespace Tests\Unit\Services;

use App\Data\Object\GetObjectData;
use App\Data\Object\StoreObjectData;
use App\Interfaces\ObjectRepositoryInterface;
use App\Models\Objects;
use App\Services\ObjectServices;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class ObjectServicesTest extends TestCase
{
    protected $objectRepositoryMock;
    protected $objectServices;

    public function setUp(): void
    {
        parent::setUp();
        // Mock the repository interface
        $this->objectRepositoryMock = Mockery::mock(ObjectRepositoryInterface::class);
        // Instantiate the service with the mock
        $this->objectServices = new ObjectServices($this->objectRepositoryMock);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function all_returns_collection_from_repository()
    {
        // Arrange
        $expectedCollection = new Collection([new Objects(['key' => 'test'])]);
        $this->objectRepositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($expectedCollection);

        // Act
        $result = $this->objectServices->all();

        // Assert
        $this->assertEquals($expectedCollection, $result);
        $this->assertInstanceOf(Collection::class, $result);
    }

    /**
     * @test
     */
    public function all_returns_empty_collection_if_repository_returns_null()
    {
        // Arrange
        $this->objectRepositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn(new Collection());

        // Act
        $result = $this->objectServices->all();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertTrue($result->isEmpty());
    }

    /**
     * @test
     */
    public function store_creates_data_and_returns_formatted_timestamp()
    {
        // Arrange
        $storeDataDto = StoreObjectData::fromArray(['key' => 'my-key', 'value' => 'my-value']);
        $createdObject = new Objects(['key' => 'my-key', 'value' => 'my-value']);
        $createdObject->created_at = now()->timestamp; // Set a timestamp

        $this->objectRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($storeDataDto)
            ->andReturn($createdObject);

        // Act
        $result = $this->objectServices->store($storeDataDto);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('Time', $result);
        $this->assertNotEmpty($result['Time']);
    }

    /**
     * @test
     */
    public function find_returns_version_object_from_repository()
    {
        // Arrange
        $key = 'find-key';
        $request = new \Illuminate\Http\Request();
        $getObjectDto = GetObjectData::fromRequest($key, $request);
        $expectedObject = new Objects(['key' => $key, 'value' => 'some-value']);
        
        $this->objectRepositoryMock
            ->shouldReceive('findByIdWithQuery')
            ->once()
            ->with($getObjectDto)
            ->andReturn($expectedObject);
            
        // Act
        $result = $this->objectServices->find($getObjectDto);

        // Assert
        $this->assertInstanceOf(Objects::class, $result);
        $this->assertEquals($expectedObject, $result);
    }
    
    /**
     * @test
     */
    public function find_returns_null_when_version_not_found()
    {
        // Arrange
        $key = 'non-existent-key';
        $request = new \Illuminate\Http\Request();
        $getObjectDto = GetObjectData::fromRequest($key, $request);

        $this->objectRepositoryMock
            ->shouldReceive('findByIdWithQuery')
            ->once()
            ->with($getObjectDto)
            ->andReturn(null);

        // Act
        $result = $this->objectServices->find($getObjectDto);

        // Assert
        $this->assertNull($result);
    }
}
