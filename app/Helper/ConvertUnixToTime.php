<?php

namespace App\Helper;

class ConvertUnixToTime{
    public function __construct(
        public string $data
    )
    {}

    public function convert() : string
    {
        $formattedTime = date('h:i A', $this->data);
        return $formattedTime;
    }
}
