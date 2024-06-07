<?php

namespace App\Validation\JsonValidators;

use App\Validation\Infrastructure\JsonObjectValidator;
use App\Validation\Infrastructure\ValidatorInterface;
use App\Validation\Model\ErrorBag;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserJsonValidator extends JsonObjectValidator implements ValidatorInterface
{

    public function validate($object): ErrorBag
    {
        $constraints = [
            'username' => [
                new Assert\NotBlank(),
                new Assert\Length(min: 7)
            ],
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email()
            ]
        ];

        return $this->doValidate($object, $constraints, false, false);
    }
}