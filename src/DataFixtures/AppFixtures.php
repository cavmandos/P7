<?php

namespace App\DataFixtures;

use App\Entity\Mobile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $brandnames = [
            0 => "Soni",
            1 => "Apeul",
            2 => "SamSougn",
            3 => "Ouah-Ouais",
            4 => "Chia-Au-Mi"
        ];

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

        $manager->flush();
    }
}
