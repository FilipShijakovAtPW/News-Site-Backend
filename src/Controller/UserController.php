<?php

declare(strict_types=1);

namespace App\Controller;

use App\Deserialization\ControllerTraits\WorksWithJsonDecoderTrait;
use App\Entity\Dto\UserAssignRole;
use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Serialization\NormalizationGroups;
use App\Service\Interface\UserServiceInterface;
use App\Service\SerializationAndValidationService;
use App\Validation\ControllerTraits\WorksWithValidationTrait;
use App\Validation\JsonValidators\CreateUserJsonValidator;
use App\Validation\JsonValidators\UserAssignRoleJsonValidator;
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
    use WorksWithValidationTrait;
    use WorksWithJsonDecoderTrait;

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
    public function createUser(Request $request, CreateUserJsonValidator $validator): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $data = $this->getJsonDataFromRequest($request);

        $this->validate($data, $validator);

        $result = $this->serializer->deserialize(json_encode($data), User::class, 'json');

        $confirmationToken = $this->userService->createUser($result);

        return new JsonResponse([
            'confirmation-url' => $this->generateUrl('confirm-user', ['token' => $confirmationToken])
        ], Response::HTTP_CREATED);
    }

    #[Route('/assign-role', name: 'dashboard-assign-role', methods: ['POST'])]
    public function assignRoleToUser(Request $request, UserAssignRoleJsonValidator $validator): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $data = $this->getJsonDataFromRequest($request);

        $this->validate($data, $validator);

        $result = $this->serializer->deserialize(json_encode($data), UserAssignRole::class, 'json');

        $this->userService->assignRole($result);

        return new Response();
    }

    #[Route('/remove-role', name: 'dashboard-remove-role', methods: ['POST'])]
    public function removeRoleFromUser(Request $request, UserAssignRoleJsonValidator $validator): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $data = $this->getJsonDataFromRequest($request);

        $this->validate($data, $validator);

        $result = $this->serializer->deserialize(json_encode($data), UserAssignRole::class, 'json');

        $this->userService->removeRole($result);

        return new Response();
    }

}
