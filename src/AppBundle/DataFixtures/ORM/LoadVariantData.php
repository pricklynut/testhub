<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Variant;

class LoadVariantData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->save($manager, 'Земля', 'yes', 'question1');

        $this->save($manager, '3.14', 'yes', 'question2');

        $this->save($manager, '2', 'no', 'question3');
        $this->save($manager, '6', 'no', 'question3');
        $this->save($manager, '8', 'yes', 'question3');
        $this->save($manager, '4', 'no', 'question3');

        $this->save($manager, 'Преступление и наказание', 'no', 'question4');
        $this->save($manager, 'Капитанская дочка', 'yes', 'question4');
        $this->save($manager, 'Руслан и Людмила', 'yes', 'question4');
        $this->save($manager, 'Камо грядеши', 'no', 'question4');

        $this->save($manager, 'но', 'no', 'question5');
        $this->save($manager, 'ха', 'no', 'question5');
        $this->save($manager, 'тэ', 'yes', 'question5');
        $this->save($manager, 'ку', 'no', 'question5');

        $this->save($manager, 'こ', 'no', 'question6');
        $this->save($manager, 'い', 'yes', 'question6');
        $this->save($manager, 'そ', 'no', 'question6');
        $this->save($manager, 'め', 'no', 'question6');

        $this->save($manager, 'ма', 'yes', 'question7');
        $this->save($manager, 'а', 'no', 'question7');
        $this->save($manager, 'цу', 'no', 'question7');
        $this->save($manager, 'ка', 'no', 'question7');

        $this->save($manager, 'ろ', 'no', 'question8');
        $this->save($manager, 'ん', 'no', 'question8');
        $this->save($manager, 'ひ', 'no', 'question8');
        $this->save($manager, 'ぬ', 'yes', 'question8');

        $this->save($manager, 'ро', 'no', 'question9');
        $this->save($manager, 'ми', 'no', 'question9');
        $this->save($manager, 'ки', 'yes', 'question9');
        $this->save($manager, 'я', 'no', 'question9');

        $this->save($manager, 'く', 'no', 'question10');
        $this->save($manager, 'む', 'yes', 'question10');
        $this->save($manager, 'よ', 'no', 'question10');
        $this->save($manager, 'も', 'no', 'question10');

        $this->save($manager, 'о', 'yes', 'question11');
        $this->save($manager, 'ва', 'no', 'question11');
        $this->save($manager, 'и', 'no', 'question11');
        $this->save($manager, 'ё', 'no', 'question11');

        $this->save($manager, 'な', 'no', 'question12');
        $this->save($manager, 'え', 'no', 'question12');
        $this->save($manager, 'さ', 'no', 'question12');
        $this->save($manager, 'ゆ', 'yes', 'question12');

        $this->save($manager, 'хи', 'no', 'question13');
        $this->save($manager, 'ру', 'no', 'question13');
        $this->save($manager, 'фу', 'yes', 'question13');
        $this->save($manager, 'ка', 'no', 'question13');

        $this->save($manager, 'た', 'yes', 'question14');
        $this->save($manager, 'つ', 'no', 'question14');
        $this->save($manager, 'ほ', 'no', 'question14');
        $this->save($manager, 'う', 'no', 'question14');

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }

    private function save($manager, $answer, $isCorrect, $question)
    {
        $v = new Variant();
        $v->setAnswer($answer);
        $v->setIsCorrect($isCorrect);
        $v->setQuestion($this->getReference($question));

        $manager->persist($v);
    }
}
