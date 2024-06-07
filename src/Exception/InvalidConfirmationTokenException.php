<?php

namespace App\Exception;

use App\Exception\ExceptionTypes\NotFoundExceptionInterface;

class InvalidConfirmationTokenException extends BaseException implements NotFoundExceptionInterface
{

    public function __construct()
    {
        parent::__construct(
            'Invalid confirmation token'
        );
    }

    public function getErrors()
    {
        return $this->getMessage();
    }
}