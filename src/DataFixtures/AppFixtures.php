<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;

    public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));



        $admin = new user;
        $hash = $this->encoder->encodePassword($admin, "password");
        $admin->setEmail("admin@gmail.com")
            ->setFullName("admin")
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $users = [];

        for ($u = 0; $u < 5; $u++) {
            $user = new User;
            $hash = $this->encoder->encodePassword($user, "password");
            $user->setEmail("user$u@gmail.com")
                ->setFullName($faker->name())
                ->setPassword($hash);

            $users[] = $user; // Je stock des users pour les affecter à mes commandes purchase fixtures.

            $manager->persist($user);
        }


        for ($c = 0; $c < 3; $c++) {
            $category = new Category;
            $category->setName($faker->department);
            $category->setSlug(strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);

            for ($p = 0; $p < mt_rand(15, 20); $p++) {
                $product = new Product;
                $product->setName($faker->productName);
                $product->setPrice($faker->price(40, 200));
                $product->setSlug($this->slugger->slug($product->getName()));
                $product->setshortDescription($faker->paragraph($nbSentences = 2, $variableNbSentences = true));
                $product->setPicture($faker->imageUrl(400, 400, true));
                $product->setCategory($category);

                $manager->persist($product);
            }
        }

        for ($p = 0; $p < mt_rand(20, 40); $p++) { //mt_rand 4x plus rapide que rand...
            $purchase = new Purchase;

            $purchase->setFullName($faker->name)
                ->setAddress($faker->streetAddress)
                ->setPostalCode($faker->postcode)
                ->setCity($faker->city)
                ->setUser($faker->randomElement($users))
                ->setTotal(mt_rand(20, 300));

            if ($faker->boolean(90)) { //Renvoi un booleen a 90% vrai, si c'est le cas alors status = paid
                $purchase->setStatus(Purchase::STATUS_PAID);
            } //Pas de else car par défaut le status des commandes est pending donc 10% des cas ici

            $manager->persist($purchase);

        }

        $manager->flush();
    }
}
