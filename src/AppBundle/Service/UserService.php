<?php

namespace AppBundle\Service;

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
    public function findByGuestKey(string $guestKey)
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
}
