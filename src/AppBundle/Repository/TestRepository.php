<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class TestRepository
 */
class TestRepository extends EntityRepository
{
    const SEARCH_LIMIT = 100;

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
     * @param string $search
     * @param int $perPage
     * @return Paginator
     */
    public function findByPage(int $page = 1, string $search = null, int $perPage = 5)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(['test', 'tags'])
            ->from('AppBundle\Entity\Test', 'test')
            ->leftJoin('test.tags', 'tags')
            ->orderBy('test.created', 'DESC')
            ->setFirstResult(($page - 1)*$perPage)
            ->setMaxResults($perPage);

        if (!empty($search)) {
            $searchIds = $this->searchIds($search);
            if (empty($searchIds)) {
                return null;
            }
            $qb->where('test.id IN (:ids)')
                ->setParameter('ids', $searchIds);
        }

        return new Paginator($qb->getQuery(), $fetchJoin = true);
    }

    /**
     * @return mixed
     */
    public function getTotalCount(string $search = null)
    {
        if (!empty($search)) {
            return count($this->searchIds($search));
        }

        $dql = "SELECT COUNT(t.id) FROM AppBundle\Entity\Test t";
        $query = $this->getEntityManager()->createQuery($dql);

        return $query->getSingleScalarResult();
    }

    /**
     * @param string $phrase
     * @return mixed
     */
    private function searchIds(string $phrase)
    {
        $phraseParts = array_filter(preg_split('/\W+/ui', $phrase));
        $searchString = join(' | ', $phraseParts);

        $driver = $this->getDriverName();

        if ($driver === 'pdo_pgsql') {
            return $this->postgresSearch($searchString);
        }

        return $this->mysqlSearch($phrase);
    }

    /**
     * @param string $searchString
     * @return array
     */
    private function postgresSearch(string $searchString)
    {
        $sql = "SELECT id FROM tests
                WHERE fulltext_search @@ to_tsquery(:search_string)
                ORDER BY ts_rank_cd(
                  fulltext_search,
                  to_tsquery(:search_string)
                ) DESC LIMIT :search_limit";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(':search_string', $searchString, \PDO::PARAM_STR);
        $stmt->bindValue(':search_limit', self::SEARCH_LIMIT, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_NUM);

        return $stmt->fetchAll();
    }

    private function mysqlSearch(string $searchString)
    {
        $sql = "SELECT id FROM tests
                WHERE MATCH(title, description) AGAINST(:search_string)
                ORDER BY MATCH(title, description) AGAINST(:search_string) DESC
                LIMIT :search_limit";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(':search_string', $searchString, \PDO::PARAM_STR);
        $stmt->bindValue(':search_limit', self::SEARCH_LIMIT, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_NUM);

        return $stmt->fetchAll();
    }

    /**
     * @return mixed
     */
    private function getDriverName()
    {
        $params = $this->getEntityManager()->getConnection()->getParams();
        return $params['driver'];
    }
}
