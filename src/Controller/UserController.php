<?php

declare(strict_types=1);

namespace App\Controller;

use App\Deserialization\DenormalizationGroups;
use App\Entity\Dto\UserAssignRole;
use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Serialization\NormalizationGroups;
use App\Service\Interface\UserServiceInterface;
use App\Service\SerializationAndValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/dashboard/user')]
class UserController extends AbstractController
{
    public function __construct(
        private UserServiceInterface $userService,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private SerializationAndValidationService $serializationAndValidationService
    )
    {
    }

    #[Route('/', name: 'dashboard-get-all-users', methods: ['GET'])]
    public function getAllUsers(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $items = $this->userService->getAllUsers();

        return JsonResponse::fromJsonString($this->serializer->serialize($items, 'json', ['groups' => NormalizationGroups::ALL_USERS]));
    }

    #[Route('/', name: 'dashboard-create-user', methods: ['POST'])]
    public function createUser(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $result = $this->serializationAndValidationService->serializeAndValidate($request, $this->serializer, $this->validator, User::class, [DenormalizationGroups::CREATE_USER]);

        if ($result instanceof Response) {
            return $result;
        }

        $confirmationToken = $this->userService->createUser($result);

        return new JsonResponse([
            'confirmation-url' => $this->generateUrl('confirm-user', ['token' => $confirmationToken])
        ], Response::HTTP_CREATED);
    }

    #[Route('/assign-role', name: 'dashboard-assign-role', methods: ['POST'])]
    public function assignRoleToUser(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $result = $this->serializationAndValidationService->serializeAndValidate($request, $this->serializer, $this->validator, UserAssignRole::class, null);

        if ($result instanceof Response) {
            return $result;
        }

        if (!in_array($result->getRole(), [User::ROLE_ADMIN, User::ROLE_EDITOR, User::ROLE_WRITER])) {
            return new Response(
                "Role should be one of the following [ROLE_ADMIN, ROLE_EDITOR, ROLE_WRITER]",
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->userService->assignRole($result);
        } catch (UserNotFoundException $exception) {
            return new Response($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new Response();
    }

    #[Route('/remove-role', name: 'dashboard-remove-role', methods: ['POST'])]
    public function removeRoleFromUser(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $result = $this->serializationAndValidationService->serializeAndValidate($request, $this->serializer, $this->validator, UserAssignRole::class, null);

        if ($result instanceof Response) {
            return $result;
        }

        if (!in_array($result->getRole(), [User::ROLE_ADMIN, User::ROLE_EDITOR, User::ROLE_WRITER])) {
            return new Response(
                "Role should be one of the following [ROLE_ADMIN, ROLE_EDITOR, ROLE_WRITER]",
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->userService->removeRole($result);
        } catch (UserNotFoundException $exception) {
            return new Response($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new Response();
    }

}
