<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class DynamicKeyStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * This validates that the request body is an object with exactly one key.
     */
    public function rules(): array
    {
        return [];
    }

    public function passedValidation()
    {
        $data = $this->all();

        if (!is_array($data) || count($data) !== 1) {
            throw ValidationException::withMessages([
                'body' => 'The request body must be a JSON object with a single key-value pair.'
            ]);
        }
    }

    /**
     * Extracts the dynamic key and value from the validated request.
     *
     * @return array{key: string, value: mixed}
     */
    public function getDynamicKeyAndValue(): array
    {   
        $data = $this->all();
        return [
            'key' => key($data),
            'value' => current($data)
        ];
    }
}