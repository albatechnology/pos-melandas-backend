<?php

namespace App\Rules;

use App\Services\CoreService;
use Illuminate\Contracts\Validation\Rule;

class NormalisedRule implements Rule
{
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return CoreService::normalise($value) == $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute may only contain lowercase letters, numbers, hyphens "-"';
    }
}