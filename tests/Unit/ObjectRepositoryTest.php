<?php

namespace Tests\Integration\Repositories;

use App\Data\Object\GetObjectData;
use App\Data\Object\StoreObjectData;
use App\Models\Objects;
use App\Repositories\ObjectRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ObjectRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private $objectRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->objectRepository = new ObjectRepository();
    }

    /** @test */
    public function get_all_returns_all_objects()
    {
        // Arrange
        Objects::factory()->count(3)->create();

        // Act
        $result = $this->objectRepository->getAll();

        // Assert
        $this->assertCount(3, $result);
    }

    /** @test */
    public function create_stores_a_new_object_in_the_database()
    {
        // Arrange
        $data = ['key' => 'new-key', 'value' => 'new-value'];
        $storeObjectData = StoreObjectData::fromArray($data);

        // Act
        $object = $this->objectRepository->create($storeObjectData);

        // Assert
        $this->assertInstanceOf(Objects::class, $object);
        $this->assertEquals('new-key', $object->key);
    }

    /** @test */
    public function find_by_key_with_query_finds_the_value_object()
    {
        // Arrange
        $key = 'my-key';
        Objects::factory()->create(['key' => $key]);

        $getObjectData = GetObjectData::fromRequest($key, new Request());

        // Act
        $result = $this->objectRepository->findByIdWithQuery($getObjectData);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($key, $result->key);
    }
    
    /** @test */
    public function find_by_key_with_query_timestamp_finds_the_value_object()
    {
        // Arrange
        $key = 'my-key';
        $data = Objects::factory()->create(['key' => $key]); // A future version

        $timestampForQuery = $data->created_at;
        $request = new Request(['timestamp' => $timestampForQuery]);

        $getObjectData = GetObjectData::fromRequest($key, $request);
        
        // Act
        $result = $this->objectRepository->findByIdWithQuery($getObjectData);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($data->id, $result->id);
    }

    /** @test */
    public function find_by_id_with_query_returns_null_for_nonexistent_key()
    {
        // Arrange
        $getObjectData = GetObjectData::fromRequest('nonexistent-key', new Request());

        // Act
        $result = $this->objectRepository->findByIdWithQuery($getObjectData);

        // Assert
        $this->assertNull($result);
    }
}
