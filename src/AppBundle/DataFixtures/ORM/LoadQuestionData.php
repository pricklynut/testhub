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
        $question1 = $this->save($manager, 'Назовите третью планету от Солнца.', 'test1', 'string_typein', 2);
        $question2 = $this->save($manager, 'Назовите число Пи (минимум 2 знака после запятой).', 'test1', 'number_typein', 1);
        $question3 = $this->save($manager, 'Сколько ног у осьминога?', 'test1', 'single_variant', 3);
        $question4 = $this->save($manager, 'Отметьте произведения Пушкина:', 'test1', 'multiple_variants', 4);

        $question5 = $this->save($manager, 'て', 'test8', 'single_variant', 1, 10);
        $question6 = $this->save($manager, 'и', 'test8', 'single_variant', 2, 10);
        $question7 = $this->save($manager, 'ま', 'test8', 'single_variant', 3, 10);
        $question8 = $this->save($manager, 'ну', 'test8', 'single_variant', 4, 10);
        $question9 = $this->save($manager, 'き', 'test8', 'single_variant', 5, 10);
        $question10 = $this->save($manager, 'му', 'test8', 'single_variant', 6, 10);
        $question11 = $this->save($manager, 'を', 'test8', 'single_variant', 7, 10);
        $question12 = $this->save($manager, 'ю', 'test8', 'single_variant', 8, 10);
        $question13 = $this->save($manager, 'ふ', 'test8', 'single_variant', 9, 10);
        $question14 = $this->save($manager, 'та', 'test8', 'single_variant', 10, 10);

        $manager->flush();

        $this->addReference('question1', $question1);
        $this->addReference('question2', $question2);
        $this->addReference('question3', $question3);
        $this->addReference('question4', $question4);
        $this->addReference('question5', $question5);
        $this->addReference('question6', $question6);
        $this->addReference('question7', $question7);
        $this->addReference('question8', $question8);
        $this->addReference('question9', $question9);
        $this->addReference('question10', $question10);
        $this->addReference('question11', $question11);
        $this->addReference('question12', $question12);
        $this->addReference('question13', $question13);
        $this->addReference('question14', $question14);
    }

    public function getOrder()
    {
        return 4;
    }

    private function save($manager, $question, $test, $type, $number, $price = 1)
    {
        $entity = new Question();
        $entity->setQuestion($question);
        $entity->setTest($this->getReference($test));
        $entity->setType($type);
        $entity->setSerialNumber($number);
        $entity->setPrice($price);
        $manager->persist($entity);

        return $entity;
    }
}
