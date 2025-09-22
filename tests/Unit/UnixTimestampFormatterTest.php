<?php

namespace Tests\Unit\Helper;

use App\Helper\UnixTimestampFormatter;
use Tests\TestCase;

class UnixTimestampFormatterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Set a consistent timezone for all tests in this file
        date_default_timezone_set('UTC');
    }

    /** @test */
    public function it_converts_timestamp_to_default_format()
    {
        // Arrange
        $timestamp = 1672531200; // 2023-01-01 00:00:00 UTC
        $formatter = new UnixTimestampFormatter($timestamp);
        
        // Act
        $result = $formatter->convert();
        
        // Assert
        $this->assertEquals('12:00 AM', $result);
    }
    
    /** @test */
    public function it_converts_timestamp_to_a_custom_format()
    {
        // Arrange
        $timestamp = 1672531200; // 2023-01-01 00:00:00 UTC
        $formatter = new UnixTimestampFormatter($timestamp, 'Y-m-d H:i:s');
        
        // Act
        $result = $formatter->convert();

        // Assert
        $this->assertEquals('2023-01-01 00:00:00', $result);
    }
}