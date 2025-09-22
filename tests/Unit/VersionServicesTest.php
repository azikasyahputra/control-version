<?php

namespace Tests\Unit\Services;

use App\Data\Version\GetVersionData;
use App\Data\Version\StoreVersionData;
use App\Interfaces\VersionRepositoryInterface;
use App\Models\Version;
use App\Services\VersionServices;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class VersionServicesTest extends TestCase
{
    protected $versionRepositoryMock;
    protected $versionServices;

    public function setUp(): void
    {
        parent::setUp();
        // Mock the repository interface
        $this->versionRepositoryMock = Mockery::mock(VersionRepositoryInterface::class);
        // Instantiate the service with the mock
        $this->versionServices = new VersionServices($this->versionRepositoryMock);
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
        $expectedCollection = new Collection([new Version(['key' => 'test'])]);
        $this->versionRepositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($expectedCollection);

        // Act
        $result = $this->versionServices->all();

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
        $this->versionRepositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn(new Collection());

        // Act
        $result = $this->versionServices->all();

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
        $storeDataDto = StoreVersionData::fromArray(['key' => 'my-key', 'value' => 'my-value']);
        $createdVersion = new Version(['key' => 'my-key', 'value' => 'my-value']);
        $createdVersion->created_at = now()->timestamp; // Set a timestamp

        $this->versionRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($storeDataDto)
            ->andReturn($createdVersion);

        // Act
        $result = $this->versionServices->store($storeDataDto);

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
        $getVersionDto = GetVersionData::fromRequest($key, $request);
        $expectedVersion = new Version(['key' => $key, 'value' => 'some-value']);
        
        $this->versionRepositoryMock
            ->shouldReceive('findByIdWithQuery')
            ->once()
            ->with($getVersionDto)
            ->andReturn($expectedVersion);
            
        // Act
        $result = $this->versionServices->find($getVersionDto);

        // Assert
        $this->assertInstanceOf(Version::class, $result);
        $this->assertEquals($expectedVersion, $result);
    }
    
    /**
     * @test
     */
    public function find_returns_null_when_version_not_found()
    {
        // Arrange
        $key = 'non-existent-key';
        $request = new \Illuminate\Http\Request();
        $getVersionDto = GetVersionData::fromRequest($key, $request);

        $this->versionRepositoryMock
            ->shouldReceive('findByIdWithQuery')
            ->once()
            ->with($getVersionDto)
            ->andReturn(null);

        // Act
        $result = $this->versionServices->find($getVersionDto);

        // Assert
        $this->assertNull($result);
    }
}
