<?php

namespace App\Controller;

use App\Commanding\Commands\ConfirmUserCommand;
use App\Deserialization\ControllerTraits\WorksWithJsonDecoderTrait;
use App\Transofmers\UserTransformer;
use App\Validation\ControllerTraits\WorksWithValidationTrait;
use App\Validation\JsonValidators\ConfirmUserJsonValidator;
use League\Tactician\CommandBus;
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
        private ValidatorInterface  $validator,
        private SerializerInterface $serializer
    )
    {
    }

    #[Route('/confirm/{token}', name: 'confirm-user', methods: ['POST'])]
    public function confirmUser(
        string                   $token,
        Request                  $request,
        ConfirmUserJsonValidator $validator,
        CommandBus               $bus
    ): Response
    {
        $data = $this->getJsonDataFromRequest($request);

        $this->validate($data, $validator);

        $bus->handle(new ConfirmUserCommand($token, $data['password']));

        return new Response();
    }
}