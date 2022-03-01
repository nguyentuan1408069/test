<?php

namespace App\Services\Response;


use App\Services\View\View;

class Response implements ResponseInterface
{
    const HTTP_OK = 200;

    const HTTP_CREATED = 201;

    const HTTP_BAD_REQUEST = 400;

    const HTTP_NOT_FOUND = 404;

    const HTTP_METHOD_NOT_ALLOWED = 405;

    const HTTP_INVALID_INPUT = 422;

    const HTTP_ERROR = 500;

    /**
     * Add header to response data
     *
     * @param string $key
     * @param string $value
     * @return \App\Services\Response\ResponseInterface
     */
    public function addHeader(string $key, string $value): ResponseInterface
    {
        header(strtolower($key).": {$value}");

        return $this;
    }

    public function response(string $message, int $code, array $data = [])
    {
        http_response_code($code);

        $this->addHeader('content-type', 'application/json');

        return print(json_encode([
            'message' => $message,
            'code' => $code,
            'data' => $data
        ]));
    }

    public function success(string $message = null, array $data = [])
    {
        return $this->response($message ?? "OK", self::HTTP_OK, $data);
    }

    public function created(string $message = null, array $data = [])
    {
        return $this->response($message ?? "Created", self::HTTP_CREATED, $data);
    }

    public function notFound(string $message = null, array $data = [])
    {
        return $this->response($message ?? "Not Found", self::HTTP_NOT_FOUND, $data);
    }

    public function view(string $path = null, array $data = [])
    {
        $view = new View($path);
        
        $view->assign($data);
    }
}