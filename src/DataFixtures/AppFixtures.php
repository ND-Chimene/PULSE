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
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setFirstname('Admin');
        $admin->setLastname('Admin');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));

        $manager->persist($admin);

        $manager->flush();
    }
}
