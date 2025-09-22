<?php

namespace Tests\Unit\Helper;

use App\Helper\CheckConvertStringJson;
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
            'valid JSON string' => [
                'input' => '{"key":"value"}',
                'expected' => '{"key":"value"}',
            ],
            'valid JSON with extra whitespace' => [
                'input' => '  {"key" : "value"}  ',
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
}
