<?php

namespace Tests\AppBundle\Helper;

use AppBundle\Helper\Pager;
use PHPUnit\Framework\TestCase;

class PagerTest extends TestCase
{
    public function testGetTotalPages()
    {
        $pager = new Pager(1, 14);
        $pager->setPerPage(5);

        $this->assertEquals(3, $pager->getTotalPages());
    }

    public function testGetFirstPage()
    {
        $pager = new Pager(2, 84);
        $pager->setPerPage(5);
        $this->assertEquals(1, $pager->getFirstPage());

        $pager = new Pager(8, 84);
        $pager->setPerPage(5);
        $this->assertEquals(6, $pager->getFirstPage());

        $pager = new Pager(15, 84);
        $pager->setPerPage(5);
        $this->assertEquals(13, $pager->getFirstPage());
    }

    public function testGetLastPage()
    {
        $pager = new Pager(2, 84);
        $pager->setPerPage(5);
        $this->assertEquals(5, $pager->getLastPage());

        $pager = new Pager(8, 84);
        $pager->setPerPage(5);
        $this->assertEquals(10, $pager->getLastPage());

        $pager = new Pager(15, 84);
        $pager->setPerPage(5);
        $this->assertEquals(17, $pager->getLastPage());
    }

    public function testIsPreviousActive()
    {
        $pager = new Pager(1, 84);
        $pager->setPerPage(5);
        $this->assertFalse($pager->isPreviousActive());

        $pager = new Pager(3, 84);
        $pager->setPerPage(5);
        $this->assertTrue($pager->isPreviousActive());
    }

    public function testIsNextActive()
    {
        $pager = new Pager(3, 84);
        $pager->setPerPage(5);
        $this->assertTrue($pager->isNextActive());

        $pager = new Pager(17, 84);
        $pager->setPerPage(5);
        $this->assertFalse($pager->isNextActive());
    }

    public function testGetPreviousPage()
    {
        $pager = new Pager(3, 84);
        $pager->setPerPage(5);
        $this->assertEquals(2, $pager->getPreviousPage());

        $pager = new Pager(3, 84);
        $pager->setPerPage(5);
        $this->assertEquals(4, $pager->getNextPage());
    }

    public function testHasPagination()
    {
        $pager = new Pager(1, 84);
        $pager->setPerPage(5);
        $this->assertTrue($pager->hasPagination());

        $pager = new Pager(1, 3);
        $pager->setPerPage(5);
        $this->assertFalse($pager->hasPagination());
    }

}
