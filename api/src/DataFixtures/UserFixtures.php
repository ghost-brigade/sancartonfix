<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public const REFERENCE = 'user-';
    public const ENTRIES = 10;

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $datas = json_decode(file_get_contents(__DIR__ . '/data/users.json'), true);

        $faker = Factory::create('fr_FR');

        foreach ($datas as $key => $data) {

            $user = new User();
            $user->setFirstname($data['firstname']);
            $user->setLastname($data['lastname']);
            $user->setGender($data['gender']);
            $user->setEmail($data['email']);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $data['password']));
            $user->setRoles($data['roles']);
            $user->setIsVerified($data['is_verified'] ?? false);
            $manager->persist($user);

            $this->setReference(self::REFERENCE . $key, $user);
        }

        for ($i = count($datas); $i < self::ENTRIES + count($datas); $i++) {

            $user = new User();
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setGender($faker->boolean);
            $user->setEmail($faker->email);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $faker->password));
            $user->setRoles(['ROLE_USER']);
            $user->setIsVerified($faker->boolean);
            $manager->persist($user);

            $this->setReference(self::REFERENCE . $i, $user);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
    }

    public function getOrder()
    {
        return 10;
    }
}
