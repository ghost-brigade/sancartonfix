<?php

namespace App\DataFixtures;

use App\Entity\Housing;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class HousingFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE = 'housing-';

    private array $users = [];

    private int $user_count = 12;

    private function populate(ObjectManager $manager): void
    {
        for ($i = 0; $i < $this->user_count; $i++) {
            $this->users[] = $this->getReference(UserFixtures::REFERENCE . $i);
        }
    }

    public function load(ObjectManager $manager): void
    {
        $this->populate($manager);

        $datas = json_decode(file_get_contents(__DIR__ . '/data/housings.json'), true);

        $faker = Factory::create('fr_FR');

        shuffle($this->users);

        foreach ($datas as $key => $data) {

            $user = array_pop($this->users);
            $category = $this->getReference(CategoryFixtures::REFERENCE . $data['category']);

            $housing = new Housing();
            $housing->setName($data['name']);
            $housing->setDescription($data['description']);
            $housing->setLatitude($faker->latitude);
            $housing->setLongitude($faker->longitude);
            $housing->setPrice($faker->randomFloat(2, 0.10, 100));
            $housing->setOwner($user);
            $housing->setCategory($category ?? null);
            $housing->setActive($faker->boolean);

            $manager->persist($housing);

            $this->setReference(self::REFERENCE . $key, $housing);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

}
