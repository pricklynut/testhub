<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Attempt;
use Doctrine\ORM\UnitOfWork;

class AttemptServiceTest extends AbstractService
{
    private static $attemptService;

    public function testCreateAndPersistAttempt()
    {
        $user = self::$em->getRepository('AppBundle:User')->find(1);
        $test = self::$em->getRepository('AppBundle:Test')->find(8);
        $attempt = self::$attemptService->createAndPersistAttempt($user, $test);

        $this->assertEquals(Attempt::class, get_class($attempt));

        $state = self::$em->getUnitOfWork()->getEntityState($attempt);
        $this->assertEquals(UnitOfWork::STATE_MANAGED, $state);
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$attemptService = self::getApplication()
            ->getKernel()
            ->getContainer()
            ->get('attempt_service');
    }

}
