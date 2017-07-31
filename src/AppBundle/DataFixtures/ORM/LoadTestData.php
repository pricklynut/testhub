<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Test;

class LoadTestData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $test1 = new Test();
        $test1->setTitle('Тестовый тест');
        $test1->setDescription('Пройдите тест, чтобы пройти тест!');
        $test1->assignAuthor($this->getReference('user1'));
        $test1->setTimeLimit(5);
        $test1->attachTag($this->getReference('tag1'));
        $test1->attachTag($this->getReference('tag3'));
        $test1->setCreated(new \DateTime());
        $manager->persist($test1);

        $test2 = new Test();
        $test2->setTitle('Тест 2');
        $test2->assignAuthor($this->getReference('user1'));
        $test2->attachTag($this->getReference('tag5'));
        $test2->setCreated(new \DateTime());
        $manager->persist($test2);

        $test3 = new Test();
        $test3->setTitle('Тест 3');
        $test3->assignAuthor($this->getReference('user2'));
        $test3->setCreated(new \DateTime());
        $manager->persist($test3);

        $test4 = new Test();
        $test4->setTitle('Тест 4');
        $test4->assignAuthor($this->getReference('user1'));
        $test4->attachTag($this->getReference('tag1'));
        $test4->attachTag($this->getReference('tag3'));
        $test4->setCreated(new \DateTime());
        $manager->persist($test4);

        $test5 = new Test();
        $test5->setTitle('Тест 5');
        $test5->assignAuthor($this->getReference('user2'));
        $test5->attachTag($this->getReference('tag5'));
        $test5->setCreated(new \DateTime());
        $manager->persist($test5);

        $test6 = new Test();
        $test6->setTitle('Тест 6');
        $test6->assignAuthor($this->getReference('user1'));
        $test6->attachTag($this->getReference('tag2'));
        $test6->attachTag($this->getReference('tag4'));
        $test6->setCreated(new \DateTime());
        $manager->persist($test6);

        $test7 = new Test();
        $test7->setTitle('Тест 7');
        $test7->assignAuthor($this->getReference('user1'));
        $test7->attachTag($this->getReference('tag4'));
        $test7->setCreated(new \DateTime());
        $manager->persist($test7);

        $manager->flush();

        $this->addReference('test1', $test1);
    }

    public function getOrder()
    {
        return 3;
    }
}
