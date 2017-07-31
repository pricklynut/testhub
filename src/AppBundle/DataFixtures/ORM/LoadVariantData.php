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
        $variant1 = new Variant();
        $variant1->setAnswer('Земля');
        $variant1->setIsCorrect('yes');
        $variant1->setQuestion($this->getReference('question1'));
        $manager->persist($variant1);

        $variant2 = new Variant();
        $variant2->setAnswer('3.14');
        $variant2->setIsCorrect('yes');
        $variant2->setQuestion($this->getReference('question2'));
        $variant2->setPrecision(2);
        $manager->persist($variant2);

        $variant3 = new Variant();
        $variant3->setAnswer('2');
        $variant3->setIsCorrect('no');
        $variant3->setQuestion($this->getReference('question3'));
        $manager->persist($variant3);

        $variant4 = new Variant();
        $variant4->setAnswer('6');
        $variant4->setIsCorrect('no');
        $variant4->setQuestion($this->getReference('question3'));
        $manager->persist($variant4);

        $variant5 = new Variant();
        $variant5->setAnswer('8');
        $variant5->setIsCorrect('yes');
        $variant5->setQuestion($this->getReference('question3'));
        $manager->persist($variant5);

        $variant6 = new Variant();
        $variant6->setAnswer('4');
        $variant6->setIsCorrect('no');
        $variant6->setQuestion($this->getReference('question3'));
        $manager->persist($variant6);

        $variant7 = new Variant();
        $variant7->setAnswer('Преступление и наказание');
        $variant7->setIsCorrect('no');
        $variant7->setQuestion($this->getReference('question4'));
        $manager->persist($variant7);

        $variant8 = new Variant();
        $variant8->setAnswer('Капитанская дочка');
        $variant8->setIsCorrect('yes');
        $variant8->setQuestion($this->getReference('question4'));
        $manager->persist($variant8);

        $variant9 = new Variant();
        $variant9->setAnswer('Руслан и Людмила');
        $variant9->setIsCorrect('yes');
        $variant9->setQuestion($this->getReference('question4'));
        $manager->persist($variant9);

        $variant10 = new Variant();
        $variant10->setAnswer('Камо грядеши');
        $variant10->setIsCorrect('no');
        $variant10->setQuestion($this->getReference('question4'));
        $manager->persist($variant10);

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
