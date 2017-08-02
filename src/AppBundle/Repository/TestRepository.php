<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class TestRepository
 */
class TestRepository extends EntityRepository
{
    /**
     * @param int $limit
     * @return Paginator
     */
    public function getRecentTests(int $limit = 5)
    {
        $dql = "SELECT test, tags FROM AppBundle\Entity\Test test
                LEFT JOIN test.tags tags ORDER BY test.created DESC";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setMaxResults($limit);

        return new Paginator($query, $fetchJoin = true);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return Paginator
     */
    public function findByPage(int $page = 1, int $perPage = 5)
    {
        $dql = "SELECT test, tags FROM AppBundle\Entity\Test test
                LEFT JOIN test.tags tags ORDER BY test.created DESC";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setMaxResults($perPage);
        $query->setFirstResult(($page - 1)*$perPage);

        return new Paginator($query, $fetchJoin = true);
    }

    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        $dql = "SELECT COUNT(t.id) FROM AppBundle\Entity\Test t";
        $query = $this->getEntityManager()->createQuery($dql);

        return $query->getSingleScalarResult();
    }
}
