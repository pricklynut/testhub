<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Test;
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
        $dql = "SELECT test, tags
                FROM AppBundle\Entity\Test test
                LEFT JOIN test.tags tags
                WHERE test.status = :status
                ORDER BY test.created DESC";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters([
            'status' => Test::STATUS_PUBLISHED,
        ]);
        $query->setMaxResults($limit);

        return new Paginator($query, $fetchJoin = true);
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
     * @param int $testId
     * @return bool|string
     */
    public function getQuestionsCount(int $testId)
    {
        $sql = "SELECT COUNT(*) FROM questions WHERE test_id = :test_id";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(':test_id', $testId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function getTotalPoints($testId)
    {
        $sql = "SELECT SUM(price) FROM questions WHERE test_id = :test_id";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(':test_id', $testId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * @param string $phrase
     * @return mixed
     */
    public function searchIds(string $phrase)
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

    /**
     * @param string $searchString
     * @return array
     */
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
