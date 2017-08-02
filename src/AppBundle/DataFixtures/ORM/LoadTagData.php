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
        $tag1->setName('литература');
        $manager->persist($tag1);

        $tag2 = new Tag();
        $tag2->setName('космос');
        $manager->persist($tag2);

        $tag3 = new Tag();
        $tag3->setName('зоология');
        $manager->persist($tag3);

        $tag4 = new Tag();
        $tag4->setName('математика');
        $manager->persist($tag4);

        $tag5 = new Tag();
        $tag5->setName('начальная школа');
        $manager->persist($tag5);

        $tag6 = new Tag();
        $tag6->setName('nasa');
        $manager->persist($tag6);

        $tag7 = new Tag();
        $tag7->setName('физика');
        $manager->persist($tag7);

        $tag8 = new Tag();
        $tag8->setName('кулон');
        $manager->persist($tag8);

        $tag9 = new Tag();
        $tag9->setName('старшая школа');
        $manager->persist($tag9);

        $tag10 = new Tag();
        $tag10->setName('числа');
        $manager->persist($tag10);

        $manager->flush();

        $this->addReference('tag1', $tag1);
        $this->addReference('tag2', $tag2);
        $this->addReference('tag3', $tag3);
        $this->addReference('tag4', $tag4);
        $this->addReference('tag5', $tag5);
        $this->addReference('tag6', $tag6);
        $this->addReference('tag7', $tag7);
        $this->addReference('tag8', $tag8);
        $this->addReference('tag9', $tag9);
        $this->addReference('tag10', $tag10);
    }

    public function getOrder()
    {
        return 2;
    }
}
