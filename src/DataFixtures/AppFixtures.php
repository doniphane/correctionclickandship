<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // --- Génération des vendeurs et produits ---
        $products = [];
        for ($v = 1; $v <= 3; $v++) {
            $vendor = new User();
            $vendor->setEmail("vendeur$v@clickcollect.test");
            $vendor->setRoles(['ROLE_VENDOR']);
            $vendor->setPassword($this->passwordHasher->hashPassword($vendor, 'password'));
            $manager->persist($vendor);
            $vendors[] = $vendor;
            // 10 produits par vendeur
            for ($p = 1; $p <= 10; $p++) {
                $product = new Product();
                $product->setName("Produit artisan v$v-$p");
                $product->setPrice(mt_rand(10, 100) + mt_rand(0, 99) / 100); // prix aléatoire entre 10.00 et 100.99
                $product->setOwner($vendor);
                $manager->persist($product);
                $products[] = $product;
            }
        }
        // --- Génération des acheteurs ---
        $buyers = [];
        for ($b = 1; $b <= 2; $b++) {
            $buyer = new User();
            $buyer->setEmail("acheteur$b@clickcollect.test");
            $buyer->setRoles(['ROLE_USER']);
            $buyer->setPassword($this->passwordHasher->hashPassword($buyer, 'password'));
            $manager->persist($buyer);
            $buyers[] = $buyer;
        }

        $manager->flush();
    }
}
