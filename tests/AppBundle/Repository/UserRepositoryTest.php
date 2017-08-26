<?php

namespace Tests\AppBundle\Repository;

class UserRepositoryTest extends AbstractRepository
{
    public function testIsGuestKeyExists()
    {
        $userRepository = self::$em->getRepository('AppBundle:User');

        $key = 'qwerty12345';
        $this->assertEquals('1', $userRepository->isGuestKeyExists($key));

        $key = 'userWithSuchKeyDoesNotExist';
        $this->assertEmpty($userRepository->isGuestKeyExists($key));
    }
}
