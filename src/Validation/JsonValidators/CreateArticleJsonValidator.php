<?php

namespace App\Validation\JsonValidators;

use App\Validation\Infrastructure\JsonObjectValidator;
use App\Validation\Infrastructure\ValidatorInterface;
use App\Validation\Model\ErrorBag;
use Symfony\Component\Validator\Constraints as Assert;

class CreateArticleJsonValidator extends JsonObjectValidator implements ValidatorInterface
{

    public function validate($object): ErrorBag
    {
        $constraints = [
            'title' => [new Assert\NotBlank(), new Assert\Length(min: 10, max: 100)],
            'summary' => [new Assert\NotBlank(), new Assert\Length(min: 10, max: 200)],
            'content' => [new Assert\NotBlank(), new Assert\Length(min: 10, max: 500)],
        ];

        return $this->doValidate($object, $constraints, false, false);
    }
}