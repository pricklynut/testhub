<?php

namespace AppBundle\Service;

use AppBundle\Entity\Attempt;
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
}
