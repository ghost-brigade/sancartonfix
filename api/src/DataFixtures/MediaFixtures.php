<?php

namespace App\DataFixtures;

use App\Entity\Media;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MediaFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE = 'media-';

    private array $housings = [];

    private function populate(ObjectManager $manager): void
    {
        for ($i = 0; $i < 4; $i++) {
            $this->housings[] = $this->getReference(HousingFixtures::REFERENCE . $i);
        }
    }

    public function load(ObjectManager $manager): void
    {
        $this->populate($manager);

        $datas = json_decode(file_get_contents(__DIR__ . '/data/medias.json'), true);

        foreach ($datas as $key => $data) {

            shuffle($this->housings);
            $housing = $this->housings[0];

            $media = new Media();
            $media->setName($data['name']);
            $media->setHousing($housing);

            $manager->persist($media);

            $this->setReference(self::REFERENCE . $key, $media);

        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            HousingFixtures::class,
        ];
    }

    public function getOrder()
    {
        return 40;
    }
}
