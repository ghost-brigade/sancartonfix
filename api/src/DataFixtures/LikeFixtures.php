<?php

namespace App\DataFixtures;

use App\Entity\Like;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LikeFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE = 'media-';
    public const ENTRIES = 100;

    private array $users = [];
    private array $housings = [];

    private $user_count = 12;
    private $housing_count = 4;

    private function populate(ObjectManager $manager): void
    {
        for ($i = 0; $i < $this->user_count; $i++) {
            $this->users[] = $this->getReference(UserFixtures::REFERENCE . $i);
        }
        for ($i = 0; $i < $this->housing_count; $i++) {
            $this->housings[] = $this->getReference(HousingFixtures::REFERENCE . $i);
        }
    }

    public function load(ObjectManager $manager): void
    {
        $this->populate($manager);

        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::ENTRIES; $i++) {
            shuffle($this->users);
            shuffle($this->housings);
            $user = $this->users[0];
            $housing = $this->housings[0];

            $like = new Like();
            $like->setLiked($faker->boolean);
            $like->setAuthor($user);
            $like->setHousing($housing);

            $manager->persist($like);

            $this->setReference(self::REFERENCE . $i, $like);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            HousingFixtures::class,
            UserFixtures::class,
        ];
    }

}
