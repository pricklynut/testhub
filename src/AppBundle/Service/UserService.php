<?php

namespace AppBundle\Service;

class UserService extends AbstractService
{
    private $userRepo;

    public function __construct($doctrine)
    {
        parent::__construct($doctrine);

        $this->userRepo = $this->em->getRepository('AppBundle:User');
    }

    public function findByGuestKey(string $guestKey)
    {
        if (empty($guestKey)) {
            return null;
        }

        return $this->userRepo->findOneBy(['guestKey' => $guestKey]);
    }
}
