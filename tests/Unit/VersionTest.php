<?php

namespace Tests\Unit;

use App\Data\Version\StoreVersionData;
use App\Models\Version;
use App\Repositories\VersionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class VersionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test 
     * @dataProvider validDataProvider
     */
    public function store_version_dto_can_be_created_from_valid_data(array $data): void
    {
        $dto = StoreVersionData::fromArray($data);

        $this->assertInstanceOf(StoreVersionData::class, $dto);
        $this->assertEquals($data['key'], $dto->key);
        $this->assertEquals($data['value'], $dto->value);
    }

    /**
     * @test
     * @dataProvider invalidDataProvider
     */
    public function store_version_dto_throws_exception_for_invalid_data(array $data, string $expectedErrorKey): void
    {
        $this->expectException(ValidationException::class);

        try {
            StoreVersionData::fromArray($data);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedErrorKey, $e->errors());
            throw $e;
        }
    }

    /** @test */
    public function version_repository_find_method_returns_newest_record_without_timestamp(): void
    {
        // Arrange: Create two records for the same key at different times
        $older = Version::create(['key' => 'test_key', 'value' => 'old', 'created_at' => now()->subDay()->getTimestamp()]);
        $newer = Version::create(['key' => 'test_key', 'value' => 'new', 'created_at' => now()->getTimestamp()]);
        
        $repository = new VersionRepository();
        $getData = \App\Data\Version\GetVersionData::fromRequest('test_key', new \Illuminate\Http\Request());

        // Act: Find the record without a timestamp
        $result = $repository->find($getData);

        // Assert: It should be the newer record
        $this->assertNotNull($result);
        $this->assertEquals($newer->id, $result->id);
        $this->assertEquals('new', $result->value);
    }
    
    //--- Data Providers for DTO tests ---

    public static function validDataProvider(): array
    {
        return [
            'plain string value' => [['key' => 'app_name', 'value' => 'Control Version']],
            'array value' => [['key' => 'settings', 'value' => ['theme' => 'dark']]],
            'json string value' => [['key' => 'config', 'value' => '{"enabled":true}']],
        ];
    }

    public static function invalidDataProvider(): array
    {
        return [
            'missing key' => [['value' => 'some_value'], 'key'],
            'missing value' => [['key' => 'some_key'], 'value'],
            'key is not a string' => [['key' => 123, 'value' => 'v'], 'key'],
            'value is not string or array' => [['key' => 'k', 'value' => 123], 'value'],
        ];
    }
}