<?php

namespace AppBundle\Service;

use AppBundle\Entity\Test;
use AppBundle\Entity\User;
use AppBundle\Helper\HashGenerator;

class UserService extends AbstractService
{
    private $userRepo;

    public function __construct($doctrine)
    {
        parent::__construct($doctrine);

        $this->userRepo = $this->em->getRepository('AppBundle:User');
    }

    /**
     * @param string $guestKey
     * @return User|null
     */
    public function findByGuestKey($guestKey)
    {
        if (empty($guestKey)) {
            return null;
        }

        return $this->userRepo->findOneBy(['guestKey' => $guestKey]);
    }

    /**
     * @return User
     */
    public function createAndPersistUser()
    {
        $user = new User();
        $user->setGuestKey(HashGenerator::generateHash());
        $user->setRegistered(new \DateTime());
        $this->em->persist($user);

        return $user;
    }

    /**
     * User can pass test in the following cases:
     * - if she has a guest_key cookie
     * - and if she has an active attempt
     * - and if the active attempt's test_id equals to the current test
     *
     * @param Test $test
     * @param User|null $user
     * @return bool
     */
    public function canUserPassTest($user, Test $test)
    {
        if (empty($user)) {
            return false;
        }

        $attemptRepo = $this->em->getRepository('AppBundle:Attempt');
        $activeAttempt = $attemptRepo->findActiveAttempt($user);

        if (
            empty($activeAttempt)
            or ($activeAttempt->getTest()->getId() !== $test->getId()))
        {
            return false;
        }

        return true;
    }

    public function hasGuestKey($request)
    {
        if (empty($request->query->get('guest_key'))) {
            return false;
        }

        return true;
    }

}
