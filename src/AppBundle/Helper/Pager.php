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
        if ($this->getCurrentPage() <= $this->getLinksOnPage()) {
            return 1;
        }

        if (
            $this->getCurrentPage() + $this->getLinksOnPage()
            >
            $this->getTotalPages()
        ) {
            return $this->getTotalPages() - $this->getLinksOnPage() + 1;
        }

        return intval(floor($this->getCurrentPage()/$this->getLinksOnPage()))
            * $this->getLinksOnPage() + 1;
    }

    /**
     * @return int
     */
    public function getLastPage()
    {
        if ($this->getCurrentPage() <= $this->getLinksOnPage()) {
            return $this->getLinksOnPage();
        }

        if (
            $this->getCurrentPage() + $this->getLinksOnPage()
            >
            $this->getTotalPages()
        ) {
            return $this->getTotalPages();
        }

        return $this->getFirstPage() + $this->getLinksOnPage() - 1;
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
        if ($this->getCurrentPage() === 1) {
            return 1;
        }
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
        if ($this->getCurrentPage() === $this->getTotalPages()) {
            return $this->getCurrentPage();
        }
        return $this->getCurrentPage() + 1;
    }

    /**
     * @return bool
     */
    public function hasPagination()
    {
        return $this->getTotalPages() > 1;
    }

}
