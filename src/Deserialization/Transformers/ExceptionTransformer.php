<?php

namespace App\Deserialization\Transformers;

use App\Exception\BaseException;
use League\Fractal\TransformerAbstract;

class ExceptionTransformer extends TransformerAbstract
{
    public function transform(BaseException $exception): array
    {
        return [
            'errors' => $exception->getErrors(),
        ];
    }
}