<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixtures extends Fixture
{   
    public const USER_REFERENCE = 'sample';
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    { 
        $this->passwordEncoder = $passwordEncoder;
        
    }

    public function load(ObjectManager $manager)
    {
        for ($i=0; $i < 4; $i++) { 
            $user = new User();
            $user->setUsername("user_" .($i+1));
            $user->setPassword($this->passwordEncoder->encodePassword($user, "user_" .($i+1)));
            $user->setEmail("user_" .($i+1)."@user.com");
                if ($i == 0) {
                    $user->setRoles(['ROLE_ADMIN']);
                }
            $this->addReference("user_".$i, $user);
            $manager->persist($user);
        }
        $manager->flush();
    }

}