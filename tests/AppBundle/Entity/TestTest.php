<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Question;
use AppBundle\Entity\Test;
use PHPUnit\Framework\TestCase;

class TestTest extends TestCase
{
    public function testGetMaxPoints()
    {
        $test = new Test();
        for ($i = 0; $i < 10; $i++) {
            $question = new Question();
            $question->setPrice(10);
            $test->addQuestion($question);
        }

        $this->assertEquals(100, $test->getMaxPoints());
    }

}
