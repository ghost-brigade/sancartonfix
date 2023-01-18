<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher) {}

    public function load(ObjectManager $manager): void {
        $datas = json_decode(file_get_contents(__DIR__ . '/data/users.json'), true);

        foreach ($datas as $data) {

            $user = new User();
            $user->setEmail($data['email']);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $data['password']));
            $user->setRoles($data['roles']);
            $user->setIsVerified($data['isVerified'] ?? false);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies() {}

    public function getOrder() {
        return 1;
    }
}
