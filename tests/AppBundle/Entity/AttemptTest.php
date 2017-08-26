<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Attempt;
use AppBundle\Entity\Test;
use PHPUnit\Framework\TestCase;

class AttemptTest extends TestCase
{
    public function testGetTimeLeft()
    {
        $test = new Test();
        $test->setTimeLimit(30);

        $attempt = new Attempt();
        $attempt->setStarted(new \DateTime('5 minutes ago'));
        $attempt->setTest($test);

        $this->assertEquals(25, $attempt->getTimeLeft());
    }

    public function testTimeIsUp()
    {
        $test = new Test();
        $test->setTimeLimit(30);

        $attempt = new Attempt();
        $attempt->setStarted(new \DateTime('15 minutes ago'));
        $attempt->setTest($test);

        $this->assertFalse($attempt->timeIsUp());

        $test->setTimeLimit(5);
        $this->assertTrue($attempt->timeIsUp());
    }

}
