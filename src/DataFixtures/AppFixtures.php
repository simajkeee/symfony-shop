<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 4; $i++) {
            $product = new Product();
            $product->setTitle('Product title ' . $i);
            $product->setDescription('Product Description' . $i);
            $product->setPrice(mt_rand(10, 100));
            $product->setQuantity(mt_rand(1, 15));
            $manager->persist($product);
        }

        $manager->flush();
    }
}
