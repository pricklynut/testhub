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
        $test1->attachTag($this->getReference('tag2'));
        $test1->attachTag($this->getReference('tag3'));
        $test1->attachTag($this->getReference('tag4'));
        $test1->setCreated(new \DateTime('2017-08-02 16:04:11'));
        $test1->setStatus('published');
        $manager->persist($test1);

        $test2 = new Test();
        $test2->setTitle('Тест по арифметике');
        $test2->assignAuthor($this->getReference('user1'));
        $test2->attachTag($this->getReference('tag4'));
        $test2->attachTag($this->getReference('tag5'));
        $test2->attachTag($this->getReference('tag10'));
        $test2->setCreated(new \DateTime('2017-08-02 13:25:48'));
        $test2->setStatus('published');
        $manager->persist($test2);

        $test3 = new Test();
        $test3->setTitle('Электродинамика');
        $test3->assignAuthor($this->getReference('user2'));
        $test3->attachTag($this->getReference('tag7'));
        $test3->attachTag($this->getReference('tag9'));
        $test3->attachTag($this->getReference('tag8'));
        $test3->setCreated(new \DateTime('2017-08-02 10:32:59'));
        $test3->setStatus('published');
        $manager->persist($test3);

        $test4 = new Test();
        $test4->setTitle('Основы космических полетов');
        $test4->assignAuthor($this->getReference('user1'));
        $test4->attachTag($this->getReference('tag6'));
        $test4->attachTag($this->getReference('tag2'));
        $test4->setCreated(new \DateTime('2017-08-02 10:28:30'));
        $test4->setStatus('published');
        $manager->persist($test4);

        $test5 = new Test();
        $test5->setTitle('Тест 5');
        $test5->assignAuthor($this->getReference('user2'));
        $test5->setCreated(new \DateTime('2017-08-01 23:01:17'));
        $test5->setStatus('published');
        $manager->persist($test5);

        $test6 = new Test();
        $test6->setTitle('Тест 6');
        $test6->assignAuthor($this->getReference('user1'));
        $test6->attachTag($this->getReference('tag2'));
        $test6->attachTag($this->getReference('tag4'));
        $test6->setCreated(new \DateTime('2017-08-01 21:50:44'));
        $test6->setStatus('published');
        $manager->persist($test6);

        $test7 = new Test();
        $test7->setTitle('Тест 7');
        $test7->assignAuthor($this->getReference('user1'));
        $test7->attachTag($this->getReference('tag4'));
        $test7->setCreated(new \DateTime('2017-08-01 20:14:35'));
        $test7->setStatus('published');
        $manager->persist($test7);

        $test8 = new Test();
        $test8->setTitle('Тест на знание хираганы');
        $test8->setDescription('Проверьте, насколько хорошо вы знаете азбуку '
                                .'хирагана, пройдя этот тест.');
        $test8->assignAuthor($this->getReference('user1'));
        $test8->setTimeLimit(15);
        $test8->attachTag($this->getReference('tag11'));
        $test8->attachTag($this->getReference('tag12'));
        $test8->setCreated(new \DateTime('2017-08-02 18:23:57'));
        $test8->setStatus('published');
        $manager->persist($test8);

        $manager->flush();

        $this->addReference('test1', $test1);
        $this->addReference('test8', $test8);
    }

    public function getOrder()
    {
        return 3;
    }
}
