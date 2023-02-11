<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    public const REFERENCE = 'city-';
    public function load(ObjectManager $manager): void {
        
        $datas = json_decode(file_get_contents(__DIR__ . '/data/cities.json'), true);

        $codes = [];
        foreach ($datas as $data) {
            if(in_array($data['Code_postal'], $codes)) {
                continue;
            } else {
                $city = new City();
                $city->setZipCode(intval($data['Code_postal']));
                $city->setName($data['Nom_commune']);

                $manager->persist($city);
                $codes[] = $data['Code_postal'];
                $this->setReference(self::REFERENCE . $city->getZipCode(), $city);
            }
        }

        $manager->flush();
    }

    public function getDependencies() {}
}
