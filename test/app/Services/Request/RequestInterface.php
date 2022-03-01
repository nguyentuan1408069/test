<?php

namespace App\Services\Request;

interface RequestInterface
{
    public function getMethod(): string;

    public function getPath(): string;

    public function getSegment($index);

    public function all(): array;

    public function getInput(string $key, $default = null);

    public function hasHeader(string $key): bool;

    public function getHeader(string $key);

    public function validate(array $rules);

    public function isAjax(): bool;
}