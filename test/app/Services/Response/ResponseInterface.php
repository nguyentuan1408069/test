<?php

namespace App\Services\Response;

interface ResponseInterface
{
    public function addHeader(string $key, string $value): ResponseInterface;

    public function response(string $message, int $code, array $data = []);

    public function success(string $message = null, array $data = []);

    public function created(string $message = null, array $data = []);

    public function notFound(string $message = null, array $data = []);

    public function view(string $path = '', array $data = []);
}