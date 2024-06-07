<?php

namespace App\Validation\Infrastructure;

use App\Validation\Model\ErrorBag;
use Symfony\Component\Validator\Constraints as Assert;
use \Symfony\Component\Validator\Validator\ValidatorInterface as Validator;

class JsonObjectValidator
{
    public function __construct(private Validator $validator)
    {
    }

    public function doValidate($object, $constraints, $allowMissingFields, $allowExtraFields): ErrorBag
    {
        $errors = $this->validator->validate($object, new Assert\Collection(
            fields: $constraints,
            allowExtraFields: $allowExtraFields,
            allowMissingFields: $allowMissingFields,
        ));

        return ErrorBag::fromValidationErrors($errors);
    }
}