<?php

namespace App\DataFixtures\MongoDb;

use App\Document\User;
use App\Model\Identifier\Identifier;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public const USERS = [
        [
            'username' => 'admin',
            'email' => 'admin@news.com',
            'password' => 'Pass123',
            'isConfirmed' => true,
            'roles' => [User::ROLE_ADMIN],
        ],
        [
            'username' => 'editor',
            'email' => 'editor@news.com',
            'password' => 'Pass123',
            'isConfirmed' => true,
            'roles' => [User::ROLE_EDITOR],
        ],
        [
            'username' => 'writer',
            'email' => 'writer@news.com',
            'password' => 'Pass123',
            'isConfirmed' => true,
            'roles' => [User::ROLE_WRITER],
        ],
        [
            'username' => 'reader',
            'email' => 'reader@news.com',
            'password' => 'Pass123',
            'isConfirmed' => true,
            'roles' => [],
        ],
    ];

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $USER) {
            $user = User::getDummy(
                Identifier::generate()->getId(),
                $USER['username'],
                $USER['email'],
                $USER['password'],
                $USER['isConfirmed'],
                $USER['roles'],
                $this->passwordHasher
            );

            $this->addReference($USER['username'], $user);

            $manager->persist($user);
        }

        $manager->flush();
    }
}