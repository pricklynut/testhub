<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

abstract class AbstractService
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct($doctrine)
    {
        $this->em = $doctrine->getManager();
    }
}
