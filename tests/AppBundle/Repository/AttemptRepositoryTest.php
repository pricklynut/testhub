<?php

namespace Tests\AppBundle\Repository;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Attempt;
use AppBundle\Entity\Question;

class AttemptRepositoryTest extends AbstractRepository
{
    private static $attemptRepo;

    public function testFindActiveAttempt()
    {
        $user = self::$em->getRepository('AppBundle:User')->find(1);
        $attempt = self::$attemptRepo->findActiveAttempt($user);

        $this->assertEquals(Attempt::class, get_class($attempt));

        return $user;
    }

    public function testFindActiveAttemptByTest()
    {
        $user = self::$em->getRepository('AppBundle:User')->find(1);
        $test = self::$em->getRepository('AppBundle:Test')->find(8);
        $attempt = self::$attemptRepo->findActiveAttemptByTest($user, $test);

        $this->assertEquals(Attempt::class, get_class($attempt));
    }

    /**
     * @param $user
     * @depends testFindActiveAttempt
     */
    public function testFinishActiveAttempts($user)
    {
        self::$attemptRepo->finishActiveAttempts($user);
        self::$em->flush();

        $attempt = self::$attemptRepo->findActiveAttempt($user);

        $this->assertEmpty($attempt);
    }

    public function testGetCurrentQuestion()
    {
        $question = self::$attemptRepo->getCurrentQuestion(8, 3);

        $this->assertEquals(Question::class, get_class($question));
        $this->assertEquals(3, $question->getSerialNumber());
    }

    public function testGetNextQuestionNumber()
    {
        $attempt = self::$attemptRepo->find(1);

        $questionNumber = self::$attemptRepo->getNextQuestionNumber($attempt, 3);
        $this->assertEquals(4, $questionNumber);

        $questionNumber = self::$attemptRepo->getNextQuestionNumber($attempt, 0);
        $this->assertEquals(2, $questionNumber);
    }

    public function testDeletePreviousAnswer()
    {
        $sql = "INSERT INTO answers (answer, attempt_id, question_id)
                VALUES ('loremipsumqwerty12345', 1, 10)";
        $conn = self::$em->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $id = self::findAnswerInsertedToTestDeletion();
        $this->assertGreaterThan(0, intval($id));

        $attempt = self::$attemptRepo->find(1);
        $question = self::$em->getRepository('AppBundle:Question')->find(10);
        self::$attemptRepo->deletePreviousAnswer($attempt, $question);

        $id = self::findAnswerInsertedToTestDeletion();
        $this->assertEmpty($id);
    }

    public function testHasUnansweredQuestions()
    {
        $attempt = self::$attemptRepo->find(1);

        $this->assertTrue(self::$attemptRepo->hasUnansweredQuestions($attempt));
    }

    public function testFindAttemptByUserAndTest()
    {
        $user = self::$em->getRepository('AppBundle:User')->find(1);
        $test = self::$em->getRepository('AppBundle:Test')->find(8);

        $attempt = self::$attemptRepo->findAttemptByUserAndTest($user, $test);

        $this->assertEquals(Attempt::class, get_class($attempt));
    }

    public function testGetAnswersOnQuestion()
    {
        $attempt = self::$attemptRepo->find(1);
        $question = self::$em->getRepository('AppBundle:Question')->find(5);

        $answers = self::$attemptRepo->getAnswersOnQuestion($attempt, $question);

        $this->assertTrue(is_array($answers));
        $this->assertGreaterThan(0, count($answers));
        $this->assertEquals(Answer::class, get_class($answers[0]));
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$attemptRepo = self::$em->getRepository('AppBundle:Attempt');

        self::loadExtraFixtures();
    }

    private static function loadExtraFixtures()
    {
        $sql = "INSERT INTO attempts (user_id, test_id) VALUES (1, 8)";
        $conn = self::$em->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $attemptId = $conn->lastInsertId();

        $sql = "INSERT INTO answers (answer, attempt_id, question_id) VALUES
                ('тэ', $attemptId, 5), ('ки', $attemptId, 7)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }

    private static function findAnswerInsertedToTestDeletion()
    {
        $conn = self::$em->getConnection();

        $sql = "SELECT id FROM answers WHERE attempt_id = 1 AND question_id = 10";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

}
