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

        $user = new User();
        
        $user->setEmail('contributor@contributor.fr');
        $passwordUser = 'contributor';
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $passwordUser
        );
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_CONTRIBUTOR']);
        $user->setUsername('Contributor');
        $manager->persist($user);
        $manager->flush();
        $this->addReference('contributor', $user);

        $user = new User();
        $user->setEmail('contributor2@contributor.fr');
        $passwordUser = 'contributor';
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $passwordUser
        );
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_CONTRIBUTOR']);
        $user->setUsername('Contributor2');
        $manager->persist($user);
        $manager->flush();
        $this->addReference('contributor2', $user);

        $userAdmin = new User();
        $userAdmin->setEmail('admin@admin.fr');
        $passwordUser = 'admin';
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $passwordUser
        );
        $userAdmin->setPassword($hashedPassword);
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setUsername('Administrator');
        $manager->persist($userAdmin);
        $manager->flush();
        $this->addReference('administrator', $userAdmin);
    }
}
