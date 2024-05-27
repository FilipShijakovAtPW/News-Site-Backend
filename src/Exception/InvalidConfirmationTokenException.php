<?php

namespace App\Exception;

class InvalidConfirmationTokenException extends \Exception
{

    public function __construct()
    {
        parent::__construct(
            'Invalid confirmation token'
        );
    }
}