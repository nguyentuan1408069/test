<?php

namespace App\Services\Validation\Interfaces;

interface RuleInterface
{
    public function passes(string $attribute, $value): bool;

    public function message(string $attribute, $value): string;
}