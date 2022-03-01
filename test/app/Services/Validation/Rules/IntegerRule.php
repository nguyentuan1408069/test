<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Interfaces\RuleInterface;

class IntegerRule implements RuleInterface
{
    public function passes(string $attribute, $value): bool
    {
        return false !== filter_var($value, FILTER_VALIDATE_INT);
    }

    public function message(string $attribute, $value): string
    {
        return $attribute.' must be an integer';
    }
}