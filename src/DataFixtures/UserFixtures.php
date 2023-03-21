<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher){
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $superadmin = new User();
        $superadmin->setEmail("email@email.fr");
        $plaintextPassword = "superadmin";
        $hashedPassword = $this->passwordHasher->hashPassword(
            $superadmin,
            $plaintextPassword
        );
        $superadmin->setPassword($hashedPassword);

        $superadmin->setRoles([
            "ROLE_SUPER_ADMIN",
        ]);

        $manager->persist($superadmin);

        $manager->flush();
    }
}
