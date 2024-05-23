<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }


    public function load(ObjectManager $manager)
    {
        foreach(UserFixtures::USERS as $USER)
        {
            for ($i = 0 ; $i < 10 ; $i++) {
                $user = $this->getReference($USER['username']);

                $isPublished = rand(1, 2) == 1;

                $article = new Article();
                $article
                    ->setTitle($this->faker->realText(30))
                    ->setContent($this->faker->realText())
                    ->setSummary($this->faker->realText(100))
                    ->setPublished($this->faker->dateTimeThisYear)
                    ->setUser($user)
                    ->setIsPublished($isPublished);

                $manager->persist($article);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}