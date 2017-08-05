<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Attempt;
use Doctrine\ORM\EntityRepository;

/**
 * Class AttemptRepository
 */
class AttemptRepository extends EntityRepository
{
    /**
     * @param User $user
     * @return null|object
     */
    public function findActiveAttempt($user)
    {
        return $this->findOneBy([
            'user' => $user,
            'status' => Attempt::STATUS_UNDERWAY],
            ['started' => 'desc']);
    }

    /**
     * @param User $user
     */
    public function finishActiveAttempts($user)
    {
        $activeAttempts = $this->findBy([
            'user' => $user,
            'status' => Attempt::STATUS_UNDERWAY
        ]);

        foreach ($activeAttempts as $attempt) {
            $attempt->finish();
        }
    }

    /**
     * @param int $testId
     * @param int $serialNumber
     * @return mixed
     */
    public function getCurrentQuestion(int $testId, int $serialNumber)
    {
        $dql = "SELECT q, v FROM AppBundle\Entity\Question q
                JOIN q.variants v
                JOIN q.test t
                WHERE t.id = :testId AND q.serialNumber = :serialNumber";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters([
            'testId' => $testId,
            'serialNumber' => $serialNumber,
        ]);

        return $query->getOneOrNullResult();
    }

    /**
     * @param Attempt $attempt
     * @param int $testId
     * @param int $serialNumber
     * @return bool|string
     */
    public function getNextQuestionNumber(Attempt $attempt, int $testId, int $serialNumber)
    {
        $answeredQuestionsIds = $this->getAnsweredQuestionsIds($attempt->getId());

        $allQuestionsIds = $this->getAllQuestionsIds($testId);

        $unansweredQuestionsIds = array_diff($allQuestionsIds, $answeredQuestionsIds);

        return $this->findNextQuestionNumber($unansweredQuestionsIds, $serialNumber);
    }

    /**
     * @param int $attemptId
     * @return array
     */
    private function getAnsweredQuestionsIds(int $attemptId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT question_id FROM answers WHERE attempt_id = :attempt_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':attempt_id', $attemptId, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_NUM);

        $rawArray = $stmt->fetchAll();
        return $this->normalizeArray($rawArray);
    }

    /**
     * @param int $testId
     * @return array
     */
    private function getAllQuestionsIds(int $testId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM questions WHERE test_id = :test_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':test_id', $testId, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_NUM);

        $rawArray = $stmt->fetchAll();
        return $this->normalizeArray($rawArray);
    }

    /**
     * @param array $arr
     * @return array
     */
    private function normalizeArray(array $arr)
    {
        $normalized = [];

        foreach ($arr as $el) {
            $normalized = array_merge($normalized, $el);
        }

        return $normalized;
    }

    /**
     * @param array $ids
     * @param int $sn
     * @return bool|string
     */
    private function findNextQuestionNumber(array $ids, int $sn)
    {
        $conn = $this->getEntityManager()->getConnection();

        $ids = implode(', ', $ids);

        $sql = "SELECT MIN(serial_number) FROM questions
                WHERE id IN ({$ids}) AND serial_number > :sn";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':sn', $sn, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

}
