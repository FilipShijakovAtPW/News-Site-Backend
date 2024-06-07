<?php

namespace App\Validation\ControllerTraits;

use App\Exception\EmptyRequestBodyException;
use App\Exception\ValidationFailedException;
use App\Validation\Infrastructure\ValidatorInterface;

trait WorksWithValidationTrait
{
    /**
     * @throws ValidationFailedException
     * @throws EmptyRequestBodyException
     */
    public function validate($data, ValidatorInterface $validator): void
    {
        if ($data === null) {
            throw new EmptyRequestBodyException();
        }

        $errorBag = $validator->validate($data);

        if ($errorBag->hasErrors()) {
            throw new ValidationFailedException($errorBag);
        }
    }
}