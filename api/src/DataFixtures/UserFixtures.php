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
        $users = json_decode(file_get_contents(__DIR__ . '/data/users.json'), true);

        foreach ($users as $userFixture) {

            $user = new User();
            $user->setEmail($userFixture['email']);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $userFixture['password']));
            $user->setRoles($userFixture['roles']);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies() {}

    public function getOrder() {
        return 1;
    }
}
