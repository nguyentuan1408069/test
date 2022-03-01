<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Interfaces\RuleInterface;

class RequireRule implements RuleInterface
{
    public function passes(string $attribute, $value): bool
    {
        if (is_string($value)) {
            return strlen($value) > 0;
        }

        if (is_array($value) || $value instanceof \Countable) {
            return count($value) > 0;
        }

        return false;
    }

    public function message(string $attribute, $value): string
    {
        return $attribute.' is required';
    }
}