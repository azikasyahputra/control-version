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
        if (is_array($value)) {
            $decoded = json_encode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }
        return $value;
    }
}
