<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void {
        $datas = json_decode(file_get_contents(__DIR__ . '/data/cities.json'), true);

        $codes = [];
        foreach ($datas as $data) {
            if(in_array($data['Code_postal'], $codes)) {
                continue;
            } else {
                $city = new City();
                $city->setPostalCode(intval($data['Code_postal']));
                $city->setName($data['Nom_commune']);
                $coords = explode(',', $data['coordonnees_gps']);

                $city->setLatitude(floatval(trim($coords[0] ?? null)));
                $city->setLongitude(floatval(trim($coords[1] ?? null)));

                $manager->persist($city);
                $codes[] = $data['Code_postal'];
            }
        }

        $manager->flush();
    }

    public function getDependencies() {}

    public function getOrder() {
        return 2;
    }
}
