<?php

namespace App\DataFixtures;

use App\Entity\Report;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReportFixtures extends Fixture implements DependentFixtureInterface
{
    public const ENTRIES = 5;
    private array $rentings = [];


    private function populate(ObjectManager $manager): void
    {
        for ($i = 0; $i < RentingFixtures::ENTRIES; $i++) {
            $this->rentings[] = $this->getReference(RentingFixtures::REFERENCE . $i);
        }
    }

    public function load(ObjectManager $manager): void
    {
        $this->populate($manager);

        $faker = Factory::create('fr_FR');

        shuffle($this->rentings);

        for ($i = 0; $i < self::ENTRIES; $i++) {
            $renting = array_pop($this->rentings);

            $report = new Report();
            $report->setContent($faker->text(100));
            $report->setRenting($renting);
            $report->setStatus($faker->boolean);
            $manager->persist($report);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RentingFixtures::class,
        ];
    }
}
