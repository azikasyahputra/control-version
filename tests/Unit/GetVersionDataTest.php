<?php

namespace Tests\Unit\Data;

use App\Data\Version\GetVersionData;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GetVersionDataTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated_from_a_request_with_valid_data()
    {
        // Arrange
        $key = 'test-key';
        $timestamp = 1672531200;
        $request = new Request(['timestamp' => $timestamp]);

        // Act
        $data = GetVersionData::fromRequest($key, $request);

        // Assert
        $this->assertInstanceOf(GetVersionData::class, $data);
        $this->assertEquals($key, $data->key);
        $this->assertEquals($timestamp, $data->timestamp);
    }

    /** @test */
    public function it_can_be_instantiated_from_a_request_without_a_timestamp()
    {
        // Arrange
        $key = 'test-key';
        $request = new Request();

        // Act
        $data = GetVersionData::fromRequest($key, $request);
        
        // Assert
        $this->assertInstanceOf(GetVersionData::class, $data);
        $this->assertEquals($key, $data->key);
        $this->assertNull($data->timestamp);
    }

    /** @test */
    public function it_throws_validation_exception_for_invalid_timestamp()
    {
        // Arrange
        $key = 'test-key';
        $request = new Request(['timestamp' => 'not-an-integer']);

        // Assert
        $this->expectException(ValidationException::class);

        // Act
        GetVersionData::fromRequest($key, $request);
    }
}