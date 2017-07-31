<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Tag;

class LoadTagData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tag1 = new Tag();
        $tag1->setName('тег1');
        $manager->persist($tag1);

        $tag2 = new Tag();
        $tag2->setName('тег2');
        $manager->persist($tag2);

        $tag3 = new Tag();
        $tag3->setName('тег3');
        $manager->persist($tag3);

        $tag4 = new Tag();
        $tag4->setName('тег4');
        $manager->persist($tag4);

        $tag5 = new Tag();
        $tag5->setName('тег5');
        $manager->persist($tag5);

        $manager->flush();

        $this->addReference('tag1', $tag1);
        $this->addReference('tag2', $tag2);
        $this->addReference('tag3', $tag3);
        $this->addReference('tag4', $tag4);
        $this->addReference('tag5', $tag5);
    }

    public function getOrder()
    {
        return 2;
    }
}
