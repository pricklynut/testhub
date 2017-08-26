<?php

namespace Tests\AppBundle\Repository;

use Tests\AppBundle\AbstractWebTestCaseWithFixturesSetup;

class AbstractRepository extends AbstractWebTestCaseWithFixturesSetup
{
    protected static $em;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$em = static::getApplication()
            ->getKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager();
    }
}
