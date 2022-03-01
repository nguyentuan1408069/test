<?php

namespace App\Services\Validation\Interfaces;

use App\Services\Request\RequestInterface;

interface Validator
{
    public function validate(RequestInterface $request): bool;
}