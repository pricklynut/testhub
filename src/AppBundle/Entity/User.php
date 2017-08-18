<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User
{
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=true)
     */
    private $username;
    /**
     * @var string
     *
     * @ORM\Column(name="guest_key", type="string", length=255, nullable=false)
     */
    private $guestKey;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered", type="datetime", nullable=false)
     */
    private $registered;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Attempt", mappedBy="user")
     */
    private $attempts;
    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Test", mappedBy="author")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $tests;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->attempts = new ArrayCollection();
        $this->tests = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getAttempts(): Collection
    {
        return $this->attempts;
    }

    /**
     * @return Collection
     */
    public function getTests(): Collection
    {
        return $this->tests;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getGuestKey(): string
    {
        return $this->guestKey;
    }

    /**
     * @param string $guestKey
     */
    public function setGuestKey(string $guestKey)
    {
        $this->guestKey = $guestKey;
    }

    /**
     * @return \DateTime
     */
    public function getRegistered(): \DateTime
    {
        return $this->registered;
    }

    /**
     * @param \DateTime $registered
     */
    public function setRegistered(\DateTime $registered)
    {
        $this->registered = $registered;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param Test $test
     */
    public function addTest(Test $test)
    {
        $this->tests[] = $test;
    }

    /**
     * @param Attempt $attempt
     */
    public function addAttempt(Attempt $attempt)
    {
        $this->attempts[] = $attempt;
    }

}

