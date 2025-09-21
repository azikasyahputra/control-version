<?php

namespace Tests\Unit;

use App\Data\Version\GetVersionData;
use App\Data\Version\StoreVersionData;
use App\Models\Version;
use App\Repositories\VersionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
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
    public function version_repository_find_method_returns_record_without_timestamp(): void
    {
        $newer = Version::create(['key' => 'test_key', 'value' => 'new', 'created_at' => now()->getTimestamp()]);
        
        $repository = new VersionRepository();
        $getData = GetVersionData::fromRequest('test_key', new \Illuminate\Http\Request());

        $result = $repository->find($getData);

        $this->assertNotNull($result);
        $this->assertEquals($newer->id, $result->id);
    }

    /** @test */
    public function version_repository_find_method_returns_record_with_timestamp(): void
    {
        $newer = Version::create(['key' => 'test_key', 'value' => 'new', 'created_at' => now()->getTimestamp()]);
        
        $timestampForQuery = $newer->created_at;
        $request = new Request(['timestamp' => $timestampForQuery]);

        $repository = new VersionRepository();
        $getData = GetVersionData::fromRequest('test_key', $request);

        $result = $repository->find($getData);

        $this->assertNotNull($result);
        $this->assertEquals($newer->id, $result->id);
    }
    
    //--- Data Providers for DTO tests ---

    public static function validDataProvider(): array
    {
        return [
            'plain string value' => [['key' => 'version_test', 'value' => '1.2.1']],
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