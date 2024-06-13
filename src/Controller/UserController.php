<?php

declare(strict_types=1);

namespace App\Controller;

use App\Commanding\Commands\AssignRoleToUserCommand;
use App\Commanding\Commands\CreateUserCommand;
use App\Commanding\Commands\RemoveRoleFromUserCommand;
use App\Deserialization\ControllerTraits\WorksWithFractalTrait;
use App\Deserialization\ControllerTraits\WorksWithJsonDecoderTrait;
use App\Document\User;
use App\Model\Identifier\Identifier;
use App\Model\UsersRepositoryInterface;
use App\Service\SerializationAndValidationService;
use App\Transofmers\UserTransformer;
use App\Validation\ControllerTraits\WorksWithValidationTrait;
use App\Validation\JsonValidators\CreateUserJsonValidator;
use App\Validation\JsonValidators\UserAssignRoleJsonValidator;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    use WorksWithFractalTrait;

    public function __construct(
        private UsersRepositoryInterface          $usersRepository,
        private SerializerInterface               $serializer,
        private ValidatorInterface                $validator,
        private SerializationAndValidationService $serializationAndValidationService
    )
    {
    }

    #[Route('/', name: 'dashboard-get-all-users', methods: ['GET'])]
    public function getAllUsers(UserTransformer $userTransformer): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $items = $this->usersRepository->getAllUsers();

        return $this->createJsonResponse(new Collection($items, $userTransformer));
    }

    #[Route('/', name: 'dashboard-create-user', methods: ['POST'])]
    public function createUser(
        Request                 $request,
        CreateUserJsonValidator $validator,
        CommandBus              $bus,
        UserTransformer         $userTransformer
    ): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $data = $this->getJsonDataFromRequest($request);

        $this->validate($data, $validator);

        /** @var Identifier $userId */
        $userId = $this->usersRepository->getNextIdentifier();

        $bus->handle(new CreateUserCommand($userId, $data['username'], $data['email']));

        $confirmationToken = $this->usersRepository->getUserConfirmationToken($userId);

        return $this->createJsonResponse(new Item($confirmationToken, function ($token) {
            return [
                'confirmation-url' => $this->generateUrl(
                    'confirm-user',
                    ['token' => $token]
                )
            ];
        }), Response::HTTP_CREATED);
    }

    #[Route('/assign-role', name: 'dashboard-assign-role', methods: ['POST'])]
    public function assignRoleToUser(Request $request, UserAssignRoleJsonValidator $validator, CommandBus $bus): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $data = $this->getJsonDataFromRequest($request);

        $this->validate($data, $validator);

        $bus->handle(new AssignRoleToUserCommand(
                Identifier::fromString($data['userId']),
                $data['role'])
        );

        return new Response();
    }

    #[Route('/remove-role', name: 'dashboard-remove-role', methods: ['POST'])]
    public function removeRoleFromUser(Request $request, UserAssignRoleJsonValidator $validator, CommandBus $bus): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $data = $this->getJsonDataFromRequest($request);

        $this->validate($data, $validator);

        $bus->handle(new RemoveRoleFromUserCommand(
                Identifier::fromString($data['userId']),
                $data['role'])
        );

        return new Response();
    }

}
