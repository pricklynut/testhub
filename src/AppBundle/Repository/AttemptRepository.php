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
        return $this->findOneBy(['user' => $user, 'status' => Attempt::STATUS_UNDERWAY], ['started' => 'desc']);
    }

    /**
     * @param User $user
     */
    public function finishActiveAttempts($user)
    {
        $activeAttempts = $this->findBy(['user' => $user, 'status' => Attempt::STATUS_UNDERWAY]);

        foreach ($activeAttempts as $attempt) {
            $attempt->finish();
        }
    }

}
