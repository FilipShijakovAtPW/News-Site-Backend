<?php

namespace App\Validation\JsonValidators;

use App\Validation\Infrastructure\JsonObjectValidator;
use App\Validation\Infrastructure\ValidatorInterface;
use App\Validation\Model\ErrorBag;
use Symfony\Component\Validator\Constraints as Assert;

class ConfirmUserJsonValidator extends JsonObjectValidator implements ValidatorInterface
{

    public function validate($object): ErrorBag
    {
        $constraints = [
            'password' => [
                new Assert\NotBlank(),
                new Assert\Regex(
                    "/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{7,}/",
                    message: "Password must contain at least 7 characters, 1 uppercase, 1 lowercase letter and 1 number"
                ),
            ],
            'repeatPassword' => new Assert\NotBlank(),
        ];

        $errorBag = $this->doValidate($object, $constraints, false, false);

        if (isset($object['password']) && isset($object['repeatPassword']) && $object['password'] !== $object['repeatPassword']) {
            $errorBag->addError('repeatPassword', 'Passwords should match');
        }

        return $errorBag;
    }
}