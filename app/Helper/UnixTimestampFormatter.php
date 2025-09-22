<?php

namespace App\Helper;

class UnixTimestampFormatter{
    private int $timestamp;
    private string $format;

    public function __construct(int $timestamp, string $format = 'h:i A')
    {
        $this->timestamp = $timestamp;
        $this->format = $format;
    }

    public function convert() : string
    {
        return date($this->format, $this->timestamp);
    }
}
