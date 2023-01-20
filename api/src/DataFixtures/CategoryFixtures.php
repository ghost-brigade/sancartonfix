<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const REFERENCE = 'category-';

    public function load(ObjectManager $manager): void
    {

        $datas = json_decode(file_get_contents(__DIR__ . '/data/categories.json'), true);

        foreach ($datas as $data) {

            $category = new Category();
            $category->setName($data['name']);

            $manager->persist($category);

            $this->setReference(self::REFERENCE . $category->getName(), $category);
        }

        $manager->flush();
    }

}
