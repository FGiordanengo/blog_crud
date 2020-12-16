<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) 
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $super = (new User())
            ->setUsername("florent")
            ->setEmail("florent.giordanengo@hotmail.fr")
            ->setRoles(["ROLE_USER", "ROLE_ADMIN", "ROLE_SUPER"])
            ->setIsVerified(true)
            ;
        $super->setPassword($this->encoder->encodePassword($super, 'WyE8x45Kn'));
        $manager->persist($super);

        $admin = (new User())
            ->setUsername("alain")
            ->setEmail("alain@terieur.local")
            ->setRoles(["ROLE_USER", "ROLE_ADMIN"])
            ->setIsVerified(true)
            ;
        $admin->setPassword($this->encoder->encodePassword($admin, 'WyE8x45Kn'));
        $manager->persist($admin);

        $faker = Faker\Factory::create('fr_FR');
        $roles = ["ROLE_USER", "ROLE_ADMIN"];
        for($u = 0; $u < 30; $u++) {
            $user = (new User())
            ->setUsername($faker->userName)
            ->setEmail($faker->safeEmail)
            ->setRoles($faker->randomElements($array = $roles, rand(0,2)))
            ->setPassword("password")
            ->setIsVerified($faker->boolean(80))
            ->setRegisterAt($faker->dateTimeThisMonth('now'))
            ;
            $user->setPassword($this->encoder->encodePassword($user, 'WyE8x45Kn'));
            $manager->persist($user);
        }
        
        

        $manager->flush();
    }
}
