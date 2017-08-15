<?php

namespace AppBundle\Service;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Attempt;
use AppBundle\Entity\Question;
use AppBundle\Entity\Test;
use AppBundle\Entity\User;
use AppBundle\Helper\Result;

class AttemptService extends AbstractService
{
    private $attemptRepo;

    public function __construct($doctrine)
    {
        parent::__construct($doctrine);

        $this->attemptRepo = $this->em->getRepository('AppBundle:Attempt');
    }

    public function findActiveAttempt($user)
    {
        if (empty($user)) {
            return null;
        }

        return $this->attemptRepo->findActiveAttempt($user);
    }

    public function findActiveAttemptByTest($user, $test)
    {
        if (empty($user)) {
            return null;
        }

        return $this->attemptRepo->findActiveAttemptByTest($user, $test);
    }

    public function getNextQuestionNumber($attempt, $currentNumber = 0)
    {
        if (empty($attempt)) {
            return null;
        }

        return $this->attemptRepo->getNextQuestionNumber($attempt, $currentNumber);
    }

    public function finishActiveAttempts(User $user)
    {
        $this->attemptRepo->finishActiveAttempts($user);
    }

    /**
     * @param User $user
     * @param Test $test
     * @return Attempt
     */
    public function createAndPersistAttempt(User $user, Test $test)
    {
        $attempt = new Attempt();
        $attempt->setStatus(Attempt::STATUS_UNDERWAY);
        $attempt->setStarted(new \DateTime());
        $attempt->setUser($user);
        $attempt->setTest($test);
        $this->em->persist($attempt);

        return $attempt;
    }

    /**
     * @param User $user
     * @param Test $test
     * @return true
     */
    public function timeIsUp(User $user, Test $test)
    {
        $lastAttempt = $this->attemptRepo->findActiveAttemptByTest($user, $test);

        return $lastAttempt->timeIsUp();
    }

    public function getCurrentQuestion(int $testId, int $serialNumber)
    {
        return $this->attemptRepo->getCurrentQuestion($testId, $serialNumber);
    }

    public function deletePreviousAnswer(Attempt $attempt, Question $question)
    {
        $this->attemptRepo->deletePreviousAnswer($attempt, $question);
    }

    public function populateAndPersistAnswers(Question $question, Attempt $attempt, $answersData)
    {
        switch ($question->getType()) {
            case Question::TYPE_NUMBER_TYPEIN:
            case Question::TYPE_STRING_TYPEIN:
                $answer = $answersData;
                $answer->setAttempt($attempt);
                $answer->setQuestion($question);
                $answer->setReceived(new \DateTime());
                $this->em->persist($answer);
                break;
            case Question::TYPE_SINGLE_VARIANT:
                $model = new Answer();
                $model->setQuestion($question);
                $model->setAttempt($attempt);
                $model->setReceived(new \DateTime());
                $model->setAnswer($answersData['answer']);
                $this->em->persist($model);
                break;
            case Question::TYPE_MULTIPLE_VARIANTS:
                foreach ($answersData['answer'] as $answerText) {
                    $answer = new Answer();
                    $answer->setQuestion($question);
                    $answer->setAttempt($attempt);
                    $answer->setReceived(new \DateTime());
                    $answer->setAnswer($answerText);
                    $this->em->persist($answer);
                }
                break;
        }
    }

    public function getResult(Test $test, Attempt $attempt)
    {
        $points = 0;
        $rightAnswersCount = 0;

        $questions = $test->getQuestions();

        foreach ($questions as $question) {
            $correctVariants = $question->getCorrectVariants();
            $answers = $this->attemptRepo->getAnswersOnQuestion($attempt, $question);

            $correctVariantsArray = array_map(function ($v) {
                return $v->getAnswer();
            }, $correctVariants);

            $answersArray = array_map(function ($a) {
                return $a->getAnswer();
            }, $answers);

            if (count($correctVariantsArray) !== count($answersArray)) {
                continue;
            }

            if (empty(array_diff($correctVariantsArray, $answersArray))) {
                $points += $question->getPrice();
                $rightAnswersCount++;
            }
        }

        $result = new Result();
        $result->setPoints($points);
        $result->setRightAnswersCount($rightAnswersCount);

        return $result;
    }

}
