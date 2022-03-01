<?php

namespace App\Exceptions;

use App\Services\Config\Config;
use App\Services\Response\Response;
use Throwable;

class Handler
{
    public static $mapHandlers = [
        MethodNotAllowedException::class => 'methodNotAllowedHandler',
        RouteNotFoundException::class => [
            'method' => 'routeNotFoundHandler',
            'force' => true
        ],
        ModelNotFoundException::class => [
            'method' => 'modelNotFoundHandler',
            'force' => true
        ],
        ValidationException::class => [
            'method' => 'validationHandler',
            'force' => true
        ],
        ViewNotFoundException::class => [
            'method' => 'viewNotFoundHandler',
            'force' => true
        ]
    ];

    /**
     * Render exception response or throw it
     *
     * @see \App\Exceptions\Handler::shouldHandleException()
     * @see \App\Exceptions\Handler::findHandleMethod()
     * @param \Throwable $e
     * @return int
     * @throws \Throwable
     */
    public function render(Throwable $e)
    {
        $handleMethod = $this->findHandleMethod($e);

        if (! $this->shouldHandleException($handleMethod)) {
            throw $e;
        }

        if (is_string($handleMethod)) {
            return $this->{$handleMethod}($e);
        }

        if (is_array($handleMethod)) {
            $method = array_get($handleMethod, 'method');

            return $this->{$method}($e);
        }

        return (new Response())->response("Server Error", Response::HTTP_ERROR);
    }

    /**
     * Find the exception handler
     *
     * @param \Throwable $e
     * @return mixed
     */
    protected function findHandleMethod(Throwable $e)
    {
        return collect(self::$mapHandlers)->first(function ($handleMethod, $exception) use ($e) {
            return $e instanceof $exception;
        });
    }

    /**
     * Check whether or not the exception thrown should be handle by the registered handler method
     *
     * @see \App\Exceptions\Handler::$mapHandlers
     * @param $handleMethod
     * @return bool
     */
    protected function shouldHandleException($handleMethod): bool
    {
        if (is_string($handleMethod)) {
            return true === Config::get('debug');
        }

        if (is_array($handleMethod)) {
            return true === array_get($handleMethod, 'force', true === Config::get('debug'));
        }

        return false;
    }

    protected function modelNotFoundHandler(Throwable $e)
    {
        return (new Response())->notFound($e->getMessage());
    }

    protected function methodNotAllowedHandler(Throwable $e)
    {
        return (new Response())->response("Method not allowed", Response::HTTP_METHOD_NOT_ALLOWED);
    }

    protected function routeNotFoundHandler(Throwable $e)
    {
        return (new Response())->response("Not Found", Response::HTTP_NOT_FOUND);
    }

    protected function viewNotFoundHandler(Throwable $e)
    {
        return (new Response())->response("Not Found", Response::HTTP_NOT_FOUND);
    }

    protected function validationHandler(Throwable $e)
    {
        return (new Response())->response(
            "Invalid request data",
            Response::HTTP_INVALID_INPUT,
            $e->getErrorBag()
        );
    }
}