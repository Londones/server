<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
    private $userPasswordHasherInterface;

    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        // create admin
        $user = new User;
        $user->setNom("admin");
        $user->setPrenom("admin");
        $user->setEmail("admin@gmail.com");
        $user->setPassword(
            $this->userPasswordHasherInterface->hashPassword(
                $user,
                $_ENV['ADMIN_PASSWORD']
            )
        );
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setEmailVerified(true);
        $manager->persist($user);

        // create random users
        for ($i = 1; $i <= 20; $i++) {
            $user = new User;
            $user->setNom("UserFname-" . $i);
            $user->setPrenom("UserLname-" . $i);
            $user->setEmail("user." . $i . "@gmail.com");
            $user->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $user,
                    "password"
                )
            );
            $user->setRoles(["ROLE_USER"]);
            $user->setEmailVerified(false);
            $this->addReference('user' . $i, $user);
            $manager->persist($user);
        }

        // create random prestataires
        for ($i = 1; $i <= 10; $i++) {
            $user = new User;
            $user->setNom("PrestataireFname-" . $i);
            $user->setPrenom("PrestataireLname-" . $i);
            $user->setEmail("prestataire." . $i . "@gmail.com");
            $user->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $user,
                    "password"
                )
            );
            $user->setRoles(["ROLE_PRESTATAIRE"]);
            $user->setEmailVerified(true);
            $this->addReference('prestataire' . $i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
