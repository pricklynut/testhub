<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setUsername('Василий');
        $user1->setGuestKey('qwerty12345');
        $user1->setRegistered(new \DateTime());
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('Григорий');
        $user2->setGuestKey('qwerty54321');
        $user2->setRegistered(new \DateTime());
        $manager->persist($user2);

        $manager->flush();

        $this->addReference('user1', $user1);
        $this->addReference('user2', $user2);
    }

    public function getOrder()
    {
        return 1;
    }
}
