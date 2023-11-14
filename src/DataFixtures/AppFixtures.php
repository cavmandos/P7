<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Mobile;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;
    
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $brandnames = [
            0 => "Soni",
            1 => "Apeul",
            2 => "SamSougn",
            3 => "Ouah-Ouais",
            4 => "Chia-Au-Mi"
        ];

        // Mobiles
        for ($i = 0; $i < 20; $i++) {
            $mobile = new Mobile();
            $mobile->setModel("Modèle " . $i);
            $random = array_rand($brandnames);
            $mobile->setBrandname($brandnames[$random]);
            $mobile->setDescription("Une description du mobile " . $i);
            $mobile->setPrice(rand(150, 1500));
            $mobile->setCreatedAt(new \DateTimeImmutable());
            $mobile->setStock(rand(10, 500));
            $mobile->setPicture("Une (jolie) image du mobile");
            $manager->persist($mobile);
        }

        $listCustomer = [];

        // Admin
        $customerAdmin = new Customer();
        $customerAdmin->setUsername("Admin");
        $customerAdmin->setEmail("admin@mail.com");
        $customerAdmin->setPassword($this->userPasswordHasher->hashPassword($customerAdmin, "1234"));
        $customerAdmin->setRoles(["ROLE_ADMIN"]);
        $manager->persist($customerAdmin);

        // Customer 1
        $customer1 = new Customer();
        $customer1->setUsername("GerardMOBILE");
        $customer1->setEmail("store1@mail.com");
        $customer1->setPassword($this->userPasswordHasher->hashPassword($customer1, "1234"));
        $customer1->setRoles(["ROLE_ADMIN"]);
        $manager->persist($customer1);
        $listCustomer[] = $customer1;

        // Customer 2
        $customer2 = new Customer();
        $customer2->setUsername("LaFnak");
        $customer2->setEmail("store2@mail.com");
        $customer2->setPassword($this->userPasswordHasher->hashPassword($customer2, "1234"));
        $customer2->setRoles(["ROLE_ADMIN"]);
        $manager->persist($customer2);
        $listCustomer[] = $customer2;

        // Users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFirstname('Prénom Utilisateur '. $i);
            $user->setLastname("Nom Utilisateur " . $i);
            $user->setEmail("client".$i."@mail.com");
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setCustomer($listCustomer[array_rand($listCustomer)]);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
