<?php

namespace Tests\AppBundle\Service;

use Tests\AppBundle\AbstractWebTestCaseWithFixturesSetup;

class AbstractService extends AbstractWebTestCaseWithFixturesSetup
{
    protected static $em;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$em = self::getApplication()
            ->getKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager();
    }
}
