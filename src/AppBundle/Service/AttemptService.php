<?php

namespace AppBundle\Service;

use AppBundle\Entity\Attempt;
use AppBundle\Entity\Test;
use AppBundle\Entity\User;

class AttemptService extends AbstractService
{
    private $attemptRepo;

    public function __construct($doctrine)
    {
        parent::__construct($doctrine);

        $this->attemptRepo = $this->em->getRepository('AppBundle:Attempt');
    }

    public function findActiveAttempt(User $user)
    {
        if (empty($user)) {
            return null;
        }

        return $this->attemptRepo->findActiveAttempt($user);
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
}
