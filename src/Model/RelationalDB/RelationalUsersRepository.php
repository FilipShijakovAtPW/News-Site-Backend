<?php

namespace App\Model\RelationalDB;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Model\UsersRepositoryInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class RelationalUsersRepository implements UsersRepositoryInterface
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    private function getRepository(): EntityRepository|UserRepository
    {
        return $this->entityManager->getRepository(User::class);
    }

    public function getAllUsers()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * @throws UserNotFoundException
     */
    public function getUserById(int $userId): User
    {
        $user = $this->getRepository()->find($userId);

        if ($user === null) {
            throw new UserNotFoundException($userId);
        }

        return $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function getUserByConfirmationToken(string $confirmationToken): User
    {
        $user = $this->getRepository()->findOneBy(['confirmationToken' => $confirmationToken]);

        if (!$user) {
            throw new UserNotFoundException(0);
        }

        return $user;
    }

    public function saveUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}