<?php

namespace Tests\Unit\Data;

use App\Data\Object\StoreObjectData;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StoreObjectDataTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated_from_an_array_with_valid_string_data()
    {
        // Arrange
        $data = ['key' => 'test-key', 'value' => 'test-value'];

        // Act
        $dto = StoreObjectData::fromArray($data);

        // Assert
        $this->assertInstanceOf(StoreObjectData::class, $dto);
        $this->assertEquals('test-key', $dto->key);
        $this->assertEquals('test-value', $dto->value);
    }

    /** @test */
    public function it_can_be_instantiated_with_a_json_string_value()
    {
        // Arrange
        $jsonData = json_encode(['a' => 1, 'b' => 2]);
        $data = ['key' => 'json-key', 'value' => $jsonData];

        // Act
        $dto = StoreObjectData::fromArray($data);

        // Assert
        $this->assertInstanceOf(StoreObjectData::class, $dto);
        $this->assertEquals('json-key', $dto->key);
        $this->assertEquals($jsonData, $dto->value);
    }
    
    /** @test */
    public function toArray_method_returns_correct_data()
    {
        // Arrange
        $data = ['key' => 'test-key', 'value' => 'test-value'];
        $dto = StoreObjectData::fromArray($data);
        
        // Act
        $result = $dto->toArray();
        
        // Assert
        $this->assertEquals($data, $result);
    }

    /** @test */
    public function it_throws_validation_exception_if_key_is_missing()
    {
        // Arrange
        $data = ['value' => 'test-value'];

        // Assert
        $this->expectException(ValidationException::class);

        // Act
        StoreObjectData::fromArray($data);
    }

    /** @test */
    public function it_throws_validation_exception_if_value_is_missing()
    {
        // Arrange
        $data = ['key' => 'test-key'];

        // Assert
        $this->expectException(ValidationException::class);

        // Act
        StoreObjectData::fromArray($data);
    }
}