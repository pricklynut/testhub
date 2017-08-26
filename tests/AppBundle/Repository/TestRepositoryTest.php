<?php

namespace Tests\AppBundle\Repository;

class TestRepositoryTest extends AbstractRepository
{
    private static $testRepo;

    public function testGetRecentTests()
    {
        $paginator = static::$testRepo->getRecentTests(5);

        $this->assertEquals(5, $paginator->getIterator()->count());
    }

    public function testGetTotalCount()
    {
        $this->assertEquals(8, self::$testRepo->getTotalCount());

        $searchPhrase = "asdfasfdsfff";
        $this->assertEquals(0, self::$testRepo->getTotalCount($searchPhrase));

        $searchPhrase = "хирагана";
        $this->assertEquals(1, self::$testRepo->getTotalCount($searchPhrase));
    }

    public function testGetCountByTag()
    {
        $this->assertEquals(1, self::$testRepo->getCountByTag(11));
        $this->assertEquals(0, self::$testRepo->getCountByTag(12345));
    }

    public function testGetQuestionsCount()
    {
        $this->assertEquals(10, self::$testRepo->getQuestionsCount(8));
        $this->assertEquals(0, self::$testRepo->getQuestionsCount(12345));
    }

    public function testTotalPoints()
    {
        $this->assertEquals(100, self::$testRepo->getTotalPoints(8));
        $this->assertEquals(0, self::$testRepo->getTotalPoints(12345));
    }

    public function testSearchIds()
    {
        $searchString = "хирагана";
        $ids = self::$testRepo->searchIds($searchString);

        $this->assertEquals(1, count($ids));
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$testRepo = static::$em->getRepository('AppBundle:Test');
    }

}
