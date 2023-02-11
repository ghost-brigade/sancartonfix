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
        for ($i = 0; $i < 18; $i++) {
            $this->housings[] = $this->getReference(HousingFixtures::REFERENCE . $i);
        }
    }

    public function load(ObjectManager $manager): void
    {
        $this->populate($manager);
        $i = 0;
        $j = 0;
        $k = 0;
        $datas = json_decode(file_get_contents(__DIR__ . '/data/medias2.json'), true);
        foreach($this->housings as $housing) {
            switch($housing->getCategory()->getName()){
                    
                case 'Carton':
                    for($x = 0; $x < 2; $x++){
                        $media = new Media();
                        // Assertion pour vérifier que les données sont valides
                        assert(array_key_exists('carton', $datas) && is_array($datas['carton']) && count($datas['carton']) > 0, 'Données pour carton non valides');
                        $media->setFilePath($datas['carton'][$i]);
                        $media->setHousing($housing);
                        $manager->persist($media);
                        $this->setReference(self::REFERENCE . $i, $media);
                        $i += 1;
                    }
                    break;
                case 'Tente':
                    for($x = 0; $x < 2; $x++){
                        $media = new Media();
                        // Assertion pour vérifier que les données sont valides
                        assert(array_key_exists('tente', $datas) && is_array($datas['tente']) && count($datas['tente']) > 0, 'Données pour tente non valides');
                        $media->setFilePath($datas['tente'][$j]);
                        $media->setHousing($housing);
                        $manager->persist($media);
                        $this->setReference(self::REFERENCE . $j, $media);
                        $j += 1;
                    }
                    break;
                case 'Banc':
                    for($x = 0; $x < 2; $x++){
                        $media = new Media();
                        // Assertion pour vérifier que les données sont valides
                        assert(array_key_exists('banc', $datas) && is_array($datas['banc']) && count($datas['banc']) > 0, 'Données pour banc non valides');
                        $media->setFilePath($datas['banc'][$k]);
                        $media->setHousing($housing);
                        $manager->persist($media);
                        $this->setReference(self::REFERENCE . $k, $media);
                        $k += 1;
                    }
                    break;
                default:
                    // Assertion pour vérifier que la catégorie est valide
                    assert(false, 'Catégorie de logement non valide : ' . $housing->getCategory()->getName());
                    break;
    
            }
        }
    
        $manager->flush();
    }
    
    

    public function getDependencies(): array
    {
        return [
            HousingFixtures::class,
        ];
    }

}