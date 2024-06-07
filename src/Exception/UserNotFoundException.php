<?php

namespace App\Exception;

use App\Exception\ExceptionTypes\NotFoundExceptionInterface;

class UserNotFoundException extends BaseException implements NotFoundExceptionInterface
{

    public function __construct(int $id)
    {
        parent::__construct("User with id $id not found");
    }

    public function getErrors()
    {
        return $this->getMessage();
    }
}