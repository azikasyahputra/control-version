<?php

namespace App\Helper;

class CheckConvertStringJson{
    public function __construct(
        public string $data
    )
    {}

    public function convert() : string
    {
        $value = $this->data;
        json_decode($value);
        if (json_last_error() === JSON_ERROR_NONE) {
            $value = json_encode(json_decode($value));
            
        }
        return $value;
    }
}
