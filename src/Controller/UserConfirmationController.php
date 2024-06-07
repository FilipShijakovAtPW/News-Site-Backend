<?php

namespace App\Controller;

use App\Deserialization\ControllerTraits\WorksWithJsonDecoderTrait;
use App\Entity\Dto\UserConfirm;
use App\Exception\InvalidConfirmationTokenException;
use App\Service\Interface\UserServiceInterface;
use App\Service\SerializationAndValidationService;
use App\Validation\ControllerTraits\WorksWithValidationTrait;
use App\Validation\JsonValidators\ConfirmUserJsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/user')]
class UserConfirmationController extends AbstractController
{
    use WorksWithJsonDecoderTrait;
    use WorksWithValidationTrait;

    public function __construct(
        private UserServiceInterface $userService,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer,
        private SerializationAndValidationService $serializationAndValidationService
    )
    {
    }

    #[Route('/confirm/{token}', name: 'confirm-user', methods: ['POST'])]
    public function confirmUser(string $token, Request $request, ConfirmUserJsonValidator $validator): Response
    {
        $data = $this->getJsonDataFromRequest($request);

        $this->validate($data, $validator);

        try {
            $this->userService->confirmUser($token, $this->serializer->deserialize(json_encode($data), 'json', UserConfirm::class));
        } catch (InvalidConfirmationTokenException $exception) {
            return new Response($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new Response();
    }
}