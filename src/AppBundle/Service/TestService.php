<?php

namespace AppBundle\Service;

use AppBundle\Entity\Test;
use AppBundle\Helper\Pager;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TestService extends AbstractService
{
    /**
     * @var int
     */
    private $perPage;

    private $testRepo;

    public function __construct($doctrine, int $perPage)
    {
        parent::__construct($doctrine);

        $this->perPage = $perPage;
        $this->testRepo = $this->em->getRepository("AppBundle:Test");
    }

    /**
     * Creates pager object
     *
     * @param int $page
     * @param string|null $search
     * @return Pager
     */
    public function createPagerForSearch(int $page, string $search = null)
    {
        $totalPages = $this->testRepo->getTotalCount($search);

        $pager = new Pager($page, $totalPages);
        $pager->setPerPage($this->perPage);

        return $pager;
    }

    public function createPagerForTagSearch(int $page, $tagId)
    {
        $totalPages = $this->testRepo->getCountByTag($tagId);

        $pager = new Pager($page, $totalPages);
        $pager->setPerPage($this->perPage);

        return $pager;
    }

    /**
     * Finds test items for current page and search phrase
     *
     * @param int $page
     * @param string|null $search
     * @return Paginator|null
     */
    public function findByPhrase(int $page, string $search = null)
    {
        $queryBuilder = $this->em
            ->createQueryBuilder()
            ->select(['test', 'tags'])
            ->from('AppBundle:Test', 'test')
            ->leftJoin('test.tags', 'tags')
            ->where('test.status = :status')
            ->setParameter('status', Test::STATUS_PUBLISHED)
            ->orderBy('test.created', 'DESC')
            ->setFirstResult( ($page - 1) * $this->perPage )
            ->setMaxResults($this->perPage);

        if (!empty($search)) {
            $searchIds = $this->testRepo->searchIds($search);
            if (empty($searchIds)) {
                return null;
            }
            $queryBuilder->andWhere('test.id IN (:ids)')
                ->setParameter('ids', $searchIds);
        }

        return new Paginator($queryBuilder, $fetchJoin = true);
    }

    public function findByTagId($tagId)
    {
        if (empty($tagId)) {
            return null;
        }

        $tag = $this->em->getRepository('AppBundle:Tag')->find($tagId);

        if (empty($tag)) {
            return null;
        }

        return $tag->getTests();
    }

    public function getQuestionsCount(int $testId)
    {
        return $this->testRepo->getQuestionsCount($testId);
    }

    public function getTotalPoints(int $testId)
    {
        return $this->testRepo->getTotalPoints($testId);
    }

    public function findById(int $testId)
    {
        return $this->testRepo->find($testId);
    }

}
