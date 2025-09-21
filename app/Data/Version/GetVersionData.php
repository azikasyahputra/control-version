<?php

namespace App\Data\Version;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GetVersionData {
    public function __construct(
        public readonly string $key,
        public readonly ?int $timestamp
    )
    {}

    public static function fromRequest($key, Request $request): self
    {
        $data = [
            'key' => $key,
            'timestamp'=>$request->query('timestamp') ?? null
        ];

        $validator = Validator::make($data, [
            'key' => ['required'],
            'timestamp' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        $validatedData = $validator->validated();
        
        return new self(
            key: $validatedData['key'],
            timestamp: $validatedData['timestamp']
        );
    }
}