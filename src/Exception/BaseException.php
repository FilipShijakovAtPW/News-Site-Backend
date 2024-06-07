<?php

namespace App\Exception;

abstract class BaseException extends \Exception
{
    public abstract function getErrors();
}