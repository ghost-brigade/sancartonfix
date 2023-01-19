<?php

namespace App\DataFixtures;

use App\Entity\Renting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RentingFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE = 'renting-';
    public const ENTRIES = 10;
    private int $user_count = 12;
    private int $housing_count = 4;

    private function populate(ObjectManager $manager): void
    {
        for ($i = 0; $i < 12; $i++) {
            $this->users[] = $this->getReference(UserFixtures::REFERENCE . $i);
        }
        for ($i = 0; $i < 4; $i++) {
            $this->housings[] = $this->getReference(HousingFixtures::REFERENCE . $i);
        }
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::ENTRIES; $i++) {
            $renting = new Renting();
            $renting->setDateStart(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 week', 'now')));
            $renting->setDateEnd(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('now', '+1 week')));
            $renting->setClient($this->getReference(UserFixtures::REFERENCE . $faker->numberBetween(0, $this->user_count - 1)));
            $renting->setHousing($this->getReference(HousingFixtures::REFERENCE . $faker->numberBetween(0, $this->housing_count - 1)));
            $manager->persist($renting);

            $this->setReference(self::REFERENCE . $i, $renting);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            HousingFixtures::class,
        ];
    }
}
