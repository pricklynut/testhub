<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Question;

class LoadQuestionData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $question1 = new Question();
        $question1->setQuestion('Назовите третью планету от Солнца.');
        $question1->setTest($this->getReference('test1'));
        $question1->setType('string_typein');
        $question1->setSerialNumber(2);
        $manager->persist($question1);

        $question2 = new Question();
        $question2->setQuestion('Назовите число Пи (минимум 2 знака после запятой).');
        $question2->setTest($this->getReference('test1'));
        $question2->setType('number_typein');
        $question2->setSerialNumber(1);
        $manager->persist($question2);

        $question3 = new Question();
        $question3->setQuestion('Сколько ног у осьминога?');
        $question3->setTest($this->getReference('test1'));
        $question3->setType('single_variant');
        $question3->setSerialNumber(3);
        $manager->persist($question3);

        $question4 = new Question();
        $question4->setQuestion('Отметьте произведения Пушкина:');
        $question4->setTest($this->getReference('test1'));
        $question4->setType('multiple_variants');
        $question4->setSerialNumber(4);
        $manager->persist($question4);

        $manager->flush();

        $this->addReference('question1', $question1);
        $this->addReference('question2', $question2);
        $this->addReference('question3', $question3);
        $this->addReference('question4', $question4);
    }

    public function getOrder()
    {
        return 4;
    }
}
