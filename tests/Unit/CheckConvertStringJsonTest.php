<?php

namespace Tests\Unit\Helper;

use App\Helper\CheckConvertStringJson;
use stdClass;
use Tests\TestCase;

class CheckConvertStringJsonTest extends TestCase
{
    /**
     * @test
     * @dataProvider stringJsonProvider
     */
    public function it_correctly_converts_or_retains_strings($input, $expected)
    {
        // Arrange
        $converter = new CheckConvertStringJson($input);

        // Act
        $result = $converter->convert();

        // Assert
        $this->assertEquals($expected, $result);
    }

    public static function stringJsonProvider(): array
    {
        return [
            'plain string' => [
                'input' => 'hello world',
                'expected' => 'hello world',
            ],
            'simple associative array' => [
                'input' => ['key' => 'value'],
                'expected' => '{"key":"value"}',
            ],
            'nested array' => [
                'input' => ['user' => ['id' => 1, 'active' => true]],
                'expected' => '{"user":{"id":1,"active":true}}',
            ],
            'json string' => [
                'input' => '{"key":"value"}',
                'expected' => '{"key":"value"}',
            ],
            'string that looks like json but is not' => [
                'input' => '{"key": "value",}', // trailing comma
                'expected' => '{"key": "value",}',
            ],
            'empty string' => [
                'input' => '',
                'expected' => '',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider reConvertDataProvider
     * @description Tests the re-conversion from a JSON string back to an object or a plain string.
     */
    public function it_correctly_handles_re_conversion_from_json_string($input, $expected)
    {
        // Arrange
        $converter = new CheckConvertStringJson($input);

        // Act
        $result = $converter->reConvert();

        // Assert
        $this->assertEquals($expected, $result);
    }

    public static function reConvertDataProvider(): array
    {
        $expectedObject = new stdClass();
        $expectedObject->key = 'value';

        return [
            'valid json object string' => [
                'input' => '{"key":"value"}',
                'expected' => $expectedObject,
            ],
            'valid json array string' => [
                'input' => '[1, "test", true]',
                'expected' => [1, "test", true],
            ],
            'plain string' => [
                'input' => 'not json',
                'expected' => 'not json',
            ],
            'invalid json string' => [
                'input' => '{"key": "value",}', // trailing comma
                'expected' => '{"key": "value",}',
            ],
            'empty string' => [
                'input' => '',
                'expected' => '',
            ],
        ];
    }
}
