<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ValidationException extends Exception
{
    protected $errorBag = [];

    public function __construct(array $errorBag, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->errorBag = $errorBag;
    }

    public function getErrorBag(): array
    {
        return $this->errorBag;
    }
}