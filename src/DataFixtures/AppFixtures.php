<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    # ObjectManager === EntityManagerInterface
    {
        // $product = new Product();
        // $manager->persist($product);

        $user = (new User)
            ->setEmail('admin@test.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setFirstName('Pierre')
            ->setLastName('Bertrand')
            ->setPassword(
                $this->hasher->hashPassword(
                    new User,
                    'Test1234!',
                )
            );
        $manager->persist($user);

        for ($i = 0; $i < 10; $i++) {
            $user = (new User)
                ->setEmail($this->faker->unique->email())
                ->setFirstName($this->faker->firstName())
                ->setLastName($this->faker->lastName())
                ->setRoles(
                    $this->faker->randomElements(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_EDITOR'], null)
                    # Adding ', null' confirm setRoles is an array
                )
                ->setPassword(
                    $this->hasher->hashPassword(
                        new User,
                        'Test1234!',
                    )
                );
            $manager->persist($user);
        }


        $manager->flush();
    }
}
