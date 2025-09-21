<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

class IsStringOrJson implements Rule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function passes($attribute, $value): bool
    {
        return is_string($value) || is_array($value);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'The :attribute must be a string or a valid JSON object.';
    }
}
