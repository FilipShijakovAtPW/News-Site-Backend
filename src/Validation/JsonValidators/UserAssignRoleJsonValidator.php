<?php

namespace App\Validation\JsonValidators;

use App\Entity\User;
use App\Validation\Infrastructure\JsonObjectValidator;
use App\Validation\Infrastructure\ValidatorInterface;
use App\Validation\Model\ErrorBag;
use Symfony\Component\Validator\Constraints as Assert;

class UserAssignRoleJsonValidator extends JsonObjectValidator implements ValidatorInterface
{

    public function validate($object): ErrorBag
    {
        $contraints = [
            'userId' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
                ],
            'role' => new Assert\NotBlank(),
        ];

        $errorBag = $this->doValidate($object, $contraints, false, false);

        if (isset($object['role']) && !in_array($object['role'], [User::ROLE_WRITER, User::ROLE_EDITOR, User::ROLE_ADMIN])) {
            $errorBag->addError(
                'role',
                sprintf('Role should be one of the following: [%s, %s, %s]',
                    User::ROLE_WRITER, User::ROLE_EDITOR, User::ROLE_ADMIN
                ));
        }

        return $errorBag;
    }
}