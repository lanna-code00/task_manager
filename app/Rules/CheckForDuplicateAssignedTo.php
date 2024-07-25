<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckForDuplicateAssignedTo implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        if (!is_array($value)) {
            $fail('The :attribute must be an array.')->translate();
            return;
        }

        // Remove any non-string values and make sure all elements are strings
        $value = array_filter($value, 'is_string');

        // Check for duplicates
        if (count($value) !== count(array_unique($value))) {
            $fail('The :attribute contains duplicate unique IDs.')->translate();
        }
    }
}
