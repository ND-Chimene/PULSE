<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    private $slugger;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        // Creation d'un utilisateur admin par défaut
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_IT']);
        $admin->setFirstname('Admin');
        $admin->setLastname('Admin');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin1234567'));
        $manager->persist($admin);
        $manager->flush();

        // Creation d'un utilisateur simple par défaut
        $user = new User();
        $user->setEmail('user@user.com');
        $user->setRoles(['ROLE_IT']);
        $user->setFirstname('User');
        $user->setLastname('User');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user12345678'));
        $manager->persist($user);
        $manager->flush();
    }
}
