<?php

namespace App\Controller;

use App\Deserialization\DenormalizationGroups;
use App\Entity\Dto\UserConfirm;
use App\Entity\User;
use App\Exception\InvalidConfirmationTokenException;
use App\Service\Interface\UserServiceInterface;
use App\Service\SerializationAndValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/user')]
class UserConfirmationController extends AbstractController
{
    public function __construct(
        private UserServiceInterface $userService,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer,
        private SerializationAndValidationService $serializationAndValidationService
    )
    {
    }

    #[Route('/confirm/{token}', name: 'confirm-user', methods: ['POST'])]
    public function confirmUser(string $token, Request $request): Response
    {
        $result = $this->serializationAndValidationService->serializeAndValidate($request, $this->serializer, $this->validator, UserConfirm::class, null);

        if ($result instanceof Response) {
            return $result;
        }

        if ($result->getPassword() !== $result->getRepeatPassword()) {
            return new Response('Passwords must match', Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->userService->confirmUser($token, $result);
        } catch (InvalidConfirmationTokenException $exception) {
            return new Response($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new Response();
    }
}