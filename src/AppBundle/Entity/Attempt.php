<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Attempt
 *
 * @ORM\Table(name="attempts")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AttemptRepository")
 */
class Attempt
{
    const STATUS_UNDERWAY = 'underway';

    const STATUS_FAILED = 'failed';

    const STATUS_FINISHED = 'finished';

    /**
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Answer",
     *     mappedBy="attempt"
     * )
     */
    private $answers;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="started", type="datetime", nullable=false)
     */
    private $started;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finished", type="datetime")
     */
    private $finished;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = 'underway';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\User",
     *     inversedBy="attempts"
     * )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \AppBundle\Entity\Test
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Test",
     *     inversedBy="attempts"
     * )
     * @ORM\JoinColumn(name="test_id", referencedColumnName="id")
     */
    private $test;

    /**
     * Attempt constructor.
     */
    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    /**
     * @param Answer $answer
     */
    public function addAnswer(Answer $answer)
    {
        $this->answers[] = $answer;
    }

    /**
     * @return Collection
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    /**
     * @return \DateTime
     */
    public function getStarted(): \DateTime
    {
        return $this->started;
    }

    /**
     * @param \DateTime $started
     */
    public function setStarted(\DateTime $started)
    {
        $this->started = $started;
    }

    /**
     * @return \DateTime
     */
    public function getFinished(): \DateTime
    {
        return $this->finished;
    }

    /**
     * @param \DateTime $finished
     */
    public function setFinished(\DateTime $finished)
    {
        $this->finished = $finished;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $user->addAttempt($this);
        $this->user = $user;
    }

    /**
     * @return Test
     */
    public function getTest(): Test
    {
        return $this->test;
    }

    /**
     * @param Test $test
     */
    public function setTest(Test $test)
    {
        $test->addAttempt($this);
        $this->test = $test;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function finish()
    {
        $this->status = self::STATUS_FINISHED;
    }

    public function failed()
    {
        $this->status = self::STATUS_FAILED;
    }

    public function getTimeLeft()
    {
        $wasted = $this->started->diff(new \DateTime());

        $minutes = $wasted->days * 24 * 60;
        $minutes += $wasted->h * 60;
        $minutes += $wasted->i;

        return $this->getTest()->getTimeLimit() - $minutes;
    }

    public function timeIsUp()
    {
        if (
            !empty($this->getTest()->getTimeLimit())
            and ($this->getTimeLeft() <= 0)
        ) {
            return true;
        }

        return false;
    }

}

