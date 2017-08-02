<?php

namespace AppBundle\Helper;

/**
 * Class Pager
 * Generates pagination widget
 */
class Pager
{
    /**
     * @var int
     */
    private $firstPage;

    /**
     * @var int
     */
    private $lastPage;

    /**
     * @var int
     */
    private $perPage = 5;

    /**
     * @var bool
     */
    private $showFirstAndLast = true;

    /**
     * @var int
     */
    private $linksOnPage = 5;

    /**
     * @var int
     */
    private $currentPage;

    /**
     * Pager constructor.
     * @param int $currentPage
     * @param int $totalCount
     */
    public function __construct(int $currentPage, int $totalCount)
    {
        $this->currentPage = $currentPage;
        $this->totalCount = $totalCount;
        $this->setFirstAndLastPage();
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage(int $currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage(int $perPage)
    {
        $this->perPage = $perPage;
    }

    /**
     * @return bool
     */
    public function isShowFirstAndLast(): bool
    {
        return $this->showFirstAndLast;
    }

    /**
     * @param bool $showFirstAndLast
     */
    public function setShowFirstAndLast(bool $showFirstAndLast)
    {
        $this->showFirstAndLast = $showFirstAndLast;
    }

    /**
     * @return int
     */
    public function getLinksOnPage(): int
    {
        return $this->linksOnPage;
    }

    /**
     * @param int $linksOnPage
     */
    public function setLinksOnPage(int $linksOnPage)
    {
        $this->linksOnPage = $linksOnPage;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return intval(ceil($this->totalCount/$this->perPage));
    }

    /**
     * @return int
     */
    public function getFirstPage()
    {
        return $this->firstPage;
    }

    /**
     * @return int
     */
    public function getLastPage()
    {
        return $this->lastPage;
    }

    /**
     * @return bool
     */
    public function isPreviousActive()
    {
        return $this->currentPage > 1;
    }

    /**
     * @return int
     */
    public function getPreviousPage()
    {
        return $this->getCurrentPage() - 1;
    }

    /**
     * @return bool
     */
    public function isNextActive()
    {
        return $this->currentPage < $this->getTotalPages();
    }

    /**
     * @return int
     */
    public function getNextPage()
    {
        return $this->getCurrentPage() + 1;
    }

    public function setFirstAndLastPage()
    {
        if ($this->getTotalPages() <= $this->linksOnPage) {
            $this->firstPage = 1;
            $this->lastPage = $this->getTotalPages();
            return;
        }

        if (($this->currentPage % $this->linksOnPage) === 0) {
            $firstPage = intval(floor($this->currentPage/$this->linksOnPage) - 1)
                         * $this->linksOnPage + 1;
        } else {
            $firstPage = intval(floor($this->currentPage/$this->linksOnPage))
                         * $this->linksOnPage + 1;
        }

        $lastPage = $firstPage + $this->linksOnPage - 1;

        if ($this->currentPage + $this->linksOnPage - 1 > $this->getTotalPages()) {
            $lastPage = $this->getTotalPages();
            $firstPage = $lastPage - $this->linksOnPage + 1;
        }

        $this->firstPage = $firstPage;
        $this->lastPage = $lastPage;
    }

}
