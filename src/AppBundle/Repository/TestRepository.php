<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TestRepository extends EntityRepository
{
    public function getRecentTests($limit = 5)
    {
        $dql = "SELECT test, tags FROM AppBundle\Entity\Test test
                LEFT JOIN test.tags tags ORDER BY test.created DESC";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setMaxResults($limit);

        return (new Paginator($query, $fetchJoin = true));
    }
}
