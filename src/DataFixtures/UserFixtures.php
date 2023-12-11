<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
        
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create();

        for($i = 0; $i < 6; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $passwordUser = 'test';
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $passwordUser
            );
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_CONTRIBUTOR']);
            $manager->persist($user);

        }

        $manager->flush();

        $userAdmin = new User();
        $userAdmin->setEmail('admin@admin.fr');
        $passwordUser = 'admin';
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $passwordUser
        );
        $userAdmin->setPassword($hashedPassword);
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $manager->persist($userAdmin);
        $manager->flush();
    }
}
