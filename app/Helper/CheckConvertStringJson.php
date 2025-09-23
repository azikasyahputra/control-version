<?php

namespace App\Helper;

class CheckConvertStringJson{
    public function __construct(
        public string|array $data
    )
    {}

    public function convert() : string
    {
        $value = $this->data;
        if(is_array($value)){
            $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return $value;
    }

    public function reConvert() : string|object|array
    {
        $value = $this->data;

        $decoded = json_decode($value);
        if (json_last_error() === JSON_ERROR_NONE) {
            $value = $decoded;
        }

        if(is_bool($value)){
            $value = $value ? 'true' : 'false';
        }
        
        return $value;
    }
}