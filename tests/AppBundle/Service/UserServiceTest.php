<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\UnitOfWork;

class UserServiceTest extends AbstractService
{
    private static $userService;

    public function testFindByGuestKey()
    {
        $key = 'notExistingKey';
        $user = self::$userService->findByGuestKey($key);

        $this->assertEmpty($user);

        $key = 'qwerty12345';
        $user = self::$userService->findByGuestKey($key);
        $this->assertEquals(User::class, get_class($user));
    }

    public function testCreateAndPersistUser()
    {
        $user = self::$userService->createAndPersistUser();
        $this->assertEquals(User::class, get_class($user));

        $state = self::$em->getUnitOfWork()->getEntityState($user);
        $this->assertEquals(UnitOfWork::STATE_MANAGED, $state);
    }

    public function testCanUserPassTest()
    {
        $user1 = self::$em->getRepository('AppBundle:User')->find(1);
        $user2 = self::$em->getRepository('AppBundle:User')->find(2);
        $test1 = self::$em->getRepository('AppBundle:Test')->find(8);
        $test2 = self::$em->getRepository('AppBundle:Test')->find(1);

        $this->assertTrue(self::$userService->canUserPassTest($user1, $test1));
        $this->assertFalse(self::$userService->canUserPassTest($user1, $test2));
        $this->assertFalse(self::$userService->canUserPassTest($user2, $test1));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testCheckIsUserAuthor()
    {
        $user = self::$em->getRepository('AppBundle:User')->find(2);
        $test = self::$em->getRepository('AppBundle:Test')->find(8);

        self::$userService->checkIsUserAuthor($user, $test);
    }

    public function testHasGuestKey()
    {
        $userId = self::$userService->hasGuestKey('qwerty12345');
        $this->assertTrue(boolval($userId));

        $userId = self::$userService->hasGuestKey('thisKeyDoesNotExist');
        $this->assertEmpty($userId);
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$userService = self::getApplication()
            ->getKernel()
            ->getContainer()
            ->get('user_service');

        self::loadExtraFixtures();
    }

    private static function loadExtraFixtures()
    {
        $sql = "INSERT INTO attempts (user_id, test_id) VALUES (1, 8)";
        $conn = self::$em->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }
}
