<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Interfaces\RuleInterface;

class MaxLengthRule implements RuleInterface
{
    protected $maxLength;

    public function __construct(int $maxLength)
    {
        $this->maxLength = $maxLength;
    }

    public function passes(string $attribute, $value): bool
    {
        if (is_string($value)) {
            return strlen($value) <= $this->maxLength;
        }

        if (is_array($value)) {
            return count($value) <= $this->maxLength;
        }

        return false;
    }

    public function message(string $attribute, $value): string
    {
        return is_array($value)
            ? $attribute.' must not contain more than '.$this->maxLength.' items'
            : $attribute.' must not contains more than '.$this->maxLength. ' characters';
    }
}