<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SerializationAndValidationService
{
    public function serializeAndValidate(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        string $entityClass,
        ?array $groups
    ): mixed
    {
        $data = $request->getContent();

        if (!$data) {
            return new Response('Request body should not be empty', Response::HTTP_BAD_REQUEST);
        }

        $entity = $serializer->deserialize($data, $entityClass, 'json');

        $errors = $validator->validate($entity, null, $groups);

        if (count($errors) > 0) {
            return new Response((string) $errors, Response::HTTP_BAD_REQUEST);
        }

        return $entity;
    }
}