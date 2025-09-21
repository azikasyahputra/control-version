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
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }

        return $value;
    }
}
