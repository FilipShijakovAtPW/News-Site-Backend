<?php

namespace App\Exception;

class UserCantEditOthersArticleException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Users can only edit their own article");
    }
}