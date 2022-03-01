<?php

namespace App\Services\Request;

use App\Services\Validation\Validator;

class Request implements RequestInterface
{
    protected $server;

    protected $headers;

    protected $segments;

    protected $user;

    protected $requestData = [];

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->segments = $this->parseSegments();

        $this->headers = collect(getallheaders())->mapWithKeys(function ($value, $key) {
            return [strtolower($key) => $value];
        })->toArray();
    }

    /**
     * Get the info from $_SERVER variable
     * If the $key is provided, the corresponding value of $_SERVER's key will be returned
     * Otherwise whole $_SERVER array will be returned
     *
     * @param string|null $key
     * @return array|mixed|null
     */
    public function getServerInfo(string $key = null)
    {
        if (null === $key) {
            return $this->server;
        }

        return array_get($this->server, $key);
    }

    /**
     * Parse all segments from request URI for array
     *
     * @return array
     */
    public function parseSegments(): array
    {
        return explode('/', $this->getPath());
    }

    /**
     * Get the method of current request
     *
     * @return string
     */
    public function getMethod(): string
    {
        return strtoupper($this->getServerInfo('REQUEST_METHOD'));
    }

    /**
     * Get the Request URI of current request
     *
     * @return string
     */
    public function getPath(): string
    {
        return trim($this->getServerInfo('PATH_INFO'), '/');
    }

    /**
     * Get the segment (index 1) value of current request
     *
     * @param $index
     * @return array|mixed|null
     */
    public function getSegment($index)
    {
        return array_get($this->segments, $index - 1);
    }

    /**
     * Get all input data from (POST, PUT) request or query from (GET, DELETE) request
     *
     * @return array
     */
    public function all(): array
    {
        if (0 === count($this->requestData)) {
            $method = 'get'.ucwords(strtolower($this->getMethod())).'Data';

            $this->requestData = $this->{$method}();
        }

        return $this->requestData;
    }

    public function getGetData(): array
    {
        return $_GET;
    }

    public function getPostData(): array
    {
        return $_POST;
    }

    public function getPutData(): array
    {
        $str = file_get_contents("php://input");

        parse_str($str, $output);

        return $output;
    }

    public function getDeleteData(): array
    {
        return $_GET;
    }

    /**
     * Get the value of the given key of request data
     *
     * @see \App\Services\Request\Request::all()
     * @param string $key
     * @param null $default
     * @return array|mixed|null
     */
    public function getInput(string $key, $default = null)
    {
        return array_get($this->all(), $key, $default);
    }

    /**
     * Check whether or not the given header is available in request
     *
     * @param string $key
     * @return bool
     */
    public function hasHeader(string $key): bool
    {
        return array_key_exists($key, $this->headers);
    }

    /**
     * Get the value of the given header from request
     *
     * @param string $key
     * @return array|string|null
     */
    public function getHeader(string $key)
    {
        return array_get($this->headers, $key);
    }

    /**
     * Validate the request input
     *
     * In case has any invalid input data, the \App\Exceptions\ValidationException will be thrown
     * and the request will be stopped
     *
     * @param array $rules
     * @return bool|mixed
     * @throws \App\Exceptions\ValidationException
     */
    public function validate(array $rules)
    {
        $validator = new Validator($rules);

        if ($validator->validate($this)) {
            return true;
        }

        return $validator->failedValidation();
    }

    public function isAjax(): bool 
    {
        return isset($this->server['HTTP_X_REQUESTED_WITH']) && $this->server['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
}