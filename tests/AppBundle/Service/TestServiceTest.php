<?php

namespace Tests\AppBundle\Service;

use AppBundle\Helper\Pager;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TestServiceTest extends AbstractService
{
    private static $testService;

    public function testCreatePagerForSearch()
    {
        $search = 'хирагана';
        $page = 1;
        $pager = self::$testService->createPagerForSearch($page, $search);

        $this->assertEquals(Pager::class, get_class($pager));
    }

    public function testCreatePagerForTagSearch()
    {
        $tagId = 11;
        $page = 1;
        $pager = self::$testService->createPagerForTagSearch($page, $tagId);

        $this->assertEquals(Pager::class, get_class($pager));
    }

    public function testFindByPhrase()
    {
        $paginator = self::$testService->findByPhrase(1, null);
        $this->assertEquals(Paginator::class, get_class($paginator));

        $paginator = self::$testService->findByPhrase(1, 'хирагана');
        $this->assertEquals(Paginator::class, get_class($paginator));

        $paginator = self::$testService->findByPhrase(1, 'notsearchablestring');
        $this->assertEmpty($paginator);
    }

    public function testFindByTagId()
    {
        $paginator = self::$testService->findByTagId(1, 11);
        $this->assertEquals(Paginator::class, get_class($paginator));

        $paginator = self::$testService->findByTagId(1, 12345);
        $this->assertEquals(0, $paginator->getIterator()->count());
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$testService = self::getApplication()
            ->getKernel()
            ->getContainer()
            ->get('test_service');
    }

}
