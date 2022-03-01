<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Interfaces\RuleInterface;

class StringRule implements RuleInterface
{
    public function passes(string $attribute, $value): bool
    {
        return is_string($value);
    }

    public function message(string $attribute, $value): string
    {
        return $attribute.' must be a string';
    }
}