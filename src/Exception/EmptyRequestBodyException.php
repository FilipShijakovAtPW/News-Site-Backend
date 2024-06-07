<?php

namespace App\Exception;


use App\Exception\ExceptionTypes\BadRequestExceptionInterface;

class EmptyRequestBodyException extends BaseException implements BadRequestExceptionInterface
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Request body should not be empty", $code, $previous);
    }

    public function getErrors()
    {
        return $this->getMessage();
    }
}