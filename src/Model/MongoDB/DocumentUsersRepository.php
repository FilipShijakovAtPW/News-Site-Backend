<?php

namespace App\Model\MongoDB;

use App\Document\User;
use App\Model\Identifier\Identifier;
use App\Model\UsersRepositoryInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class DocumentUsersRepository implements UsersRepositoryInterface
{
    public function __construct(private DocumentManager $documentManager)
    {
    }

    private function getRepository(): DocumentRepository
    {
        return $this->documentManager->getRepository(User::class);
    }

    public function getNextIdentifier()
    {
        return Identifier::generate();
    }

    public function getAllUsers()
    {
        return $this->getRepository()->findAll();
    }

    public function getUserById(Identifier $userIdentifier): User
    {
        return $this->getRepository()->find($userIdentifier->getId());
    }

    public function getUserByConfirmationToken(string $confirmationToken): User
    {
        return $this->getRepository()->findOneBy(['confirmationToken' => $confirmationToken]);
    }

    public function getUserByUsername(string $username)
    {
        return $this->getRepository()->findOneBy(['username' => $username]);
    }

    public function getUserConfirmationToken(Identifier $userIdentifier)
    {
        return $this->getUserById($userIdentifier)->getConfirmationToken();
    }

    public function saveUser(User $user)
    {
        $this->documentManager->persist($user);
        $this->documentManager->flush();
    }

    public function flush()
    {
        $this->documentManager->flush();
    }
}