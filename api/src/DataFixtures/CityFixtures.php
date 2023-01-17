<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void {
        $datas = json_decode(file_get_contents(__DIR__ . '/data/cities.json'), true);

        foreach ($datas as $data) {

            $city = new City();
            $city->setCode(intval($data['Code_commune_INSEE']));
            $city->setName($data['Nom_commune']);
            $city->setPostalCode(intval($data['Code_postal']));

            $coords = explode(',', $data['coordonnees_gps']);

            $city->setLatitude(floatval(trim($coords[0] ?? null)));
            $city->setLongitude(floatval(trim($coords[1] ?? null)));

            $manager->persist($city);
        }

        $manager->flush();
    }

    public function getDependencies() {}

    public function getOrder() {
        return 2;
    }
}
