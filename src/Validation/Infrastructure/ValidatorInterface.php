<?php

namespace App\Validation\Infrastructure;

use App\Validation\Model\ErrorBag;

interface ValidatorInterface
{
    public function validate($object): ErrorBag;
}