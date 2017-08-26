<?php

namespace Tests\AppBundle\Repository;

use Tests\AppBundle\AbstractWebTestCaseWithFixturesSetup;

class UserRepositoryTest extends AbstractWebTestCaseWithFixturesSetup
{
    private static $em;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$em = static::getApplication()
            ->getKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testIsGuestKeyExists()
    {
        $userRepository = self::$em->getRepository('AppBundle:User');

        $key = 'qwerty12345';
        $this->assertEquals('1', $userRepository->isGuestKeyExists($key));

        $key = 'userWithSuchKeyDoesNotExist';
        $this->assertEmpty($userRepository->isGuestKeyExists($key));
    }
}
