<?php

namespace Tests\Integration\Repositories;

use App\Data\Version\GetVersionData;
use App\Data\Version\StoreVersionData;
use App\Models\Version;
use App\Repositories\VersionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class VersionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private $versionRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->versionRepository = new VersionRepository();
    }

    /** @test */
    public function get_all_returns_all_versions()
    {
        // Arrange
        Version::factory()->count(3)->create();

        // Act
        $result = $this->versionRepository->getAll();

        // Assert
        $this->assertCount(3, $result);
    }

    /** @test */
    public function create_stores_a_new_version_in_the_database()
    {
        // Arrange
        $data = ['key' => 'new-key', 'value' => 'new-value'];
        $storeVersionData = StoreVersionData::fromArray($data);

        // Act
        $version = $this->versionRepository->create($storeVersionData);

        // Assert
        // $this->assertDatabaseHas('version', $data);
        $this->assertInstanceOf(Version::class, $version);
        $this->assertEquals('new-key', $version->key);
    }

    /** @test */
    public function find_by_key_with_query_finds_the_value_version()
    {
        // Arrange
        $key = 'my-key';
        Version::factory()->create(['key' => $key]);

        $getVersionData = GetVersionData::fromRequest($key, new Request());

        // Act
        $result = $this->versionRepository->findByIdWithQuery($getVersionData);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($key, $result->key);
    }
    
    /** @test */
    public function find_by_key_with_query_timestamp_finds_the_value_version()
    {
        // Arrange
        $key = 'my-key';
        $data = Version::factory()->create(['key' => $key]); // A future version

        $timestampForQuery = $data->created_at;
        $request = new Request(['timestamp' => $timestampForQuery]);

        $getVersionData = GetVersionData::fromRequest($key, $request);
        
        // Act
        $result = $this->versionRepository->findByIdWithQuery($getVersionData);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($data->id, $result->id);
    }

    /** @test */
    public function find_by_id_with_query_returns_null_for_nonexistent_key()
    {
        // Arrange
        $getVersionData = GetVersionData::fromRequest('nonexistent-key', new Request());

        // Act
        $result = $this->versionRepository->findByIdWithQuery($getVersionData);

        // Assert
        $this->assertNull($result);
    }
}
