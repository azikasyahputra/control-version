<?php

namespace Tests\Unit\Rules;

use App\Rules\IsStringOrJson;
use Tests\TestCase;

class IsStringOrJsonTest extends TestCase
{
    private $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = new IsStringOrJson();
    }

    /**
     * @test
     * @dataProvider validationDataProvider
     */
    public function it_validates_strings_and_arrays_correctly($value, $expected)
    {
        // Act
        $result = $this->rule->passes('value', $value);
        
        // Assert
        $this->assertEquals($expected, $result);
    }

    public static function validationDataProvider(): array
    {
        return [
            'valid string'  => ['value' => 'hello world', 'expected' => true],
            'valid array'   => ['value' => ['key' => 'value'], 'expected' => true],
            'invalid integer' => ['value' => 123, 'expected' => false],
            'invalid boolean' => ['value' => true, 'expected' => false],
            'invalid object'  => ['value' => new \stdClass(), 'expected' => false],
            'invalid null'    => ['value' => null, 'expected' => false],
        ];
    }
    
    /**
     * @test
     */
    public function message_returns_the_correct_validation_error_string()
    {
        // Act
        $message = $this->rule->message();
        
        // Assert
        $this->assertEquals('The :attribute must be a string or a valid JSON object.', $message);
    }
}