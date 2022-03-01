<?php

namespace App\Controllers;

use App\Services\Request\RequestInterface;
use App\Services\Response\ResponseInterface;

class BaseController
{
    protected $request;

    protected $response;

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}