<?php

namespace App\Data\Object;

use App\Rules\IsStringOrJson;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StoreObjectData {
    public function __construct(
        public readonly string $key,
        public readonly string|array $value
    )
    {}

    public static function fromArray(array $data): self
    {
        $validator = Validator::make($data, [
            'key' => ['required', 'string', 'max:255'],
            'value' => ['required', new IsStringOrJson()],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        $validatedData = $validator->validated();
        
        return new self(
            key: $validatedData['key'],
            value: $validatedData['value']
        );
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
        ];
    }
}