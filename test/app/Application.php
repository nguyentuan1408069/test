<?php

namespace App;

use App\Exceptions\Handler as ExceptionHandler;
use App\Services\Request\Request;
use App\Services\Request\RequestInterface;
use App\Services\Response\Response;
use App\Services\Response\ResponseInterface;
use App\Services\Router\Router;
use Throwable;

class Application
{
    protected $router;

    protected $request;

    protected $response;
    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();

        $this->router = new Router(
            $this->request(),
            $this->response()
        );
    }

    /**
     * @return mixed
     * @throws \Throwable
     */
    public function run()
    {
        $router = $this->router->loadRoutes();

        try {
            return $router->findMatchedRoutes()
                ->findAllowedMethods()
                ->handle();
        } catch (Throwable $e) {
            return (new ExceptionHandler())->render($e);
        }
    }

    public function request(): RequestInterface
    {
        return $this->request;
    }

    public function response(): ResponseInterface
    {
        return $this->response;
    }

    public function router(): Router
    {
        return $this->router;
    }
}
