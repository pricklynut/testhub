<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Test
 *
 * @ORM\Table(name="tests")
 * @ORM\Entity
 */
class Test
{
    const SHOW_ANSWERS = 'yes';
    const NOT_SHOW_ANSWERS = 'no';

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Attempt", mappedBy="test")
     */
    private $attempts;
    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Question", mappedBy="test")
     */
    private $questions;
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;
    /**
     * @var integer
     *
     * @ORM\Column(name="time_limit", type="integer", nullable=true)
     */
    private $timeLimit;
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="tests")
     */
    private $author;
    /**
     * @var string
     *
     * @ORM\Column(name="show_answers", type="string", nullable=false)
     */
    private $showAnswers = self::NOT_SHOW_ANSWERS;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="tests")
     * @ORM\JoinTable(name="test_to_tag")
     */
    private $tags;

    /**
     * Test constructor.
     */
    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->attempts = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * @param Tag $tag
     */
    public function attachTag(Tag $tag)
    {
        $this->tags[] = $tag;
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
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param Attempt $attempt
     */
    public function addAttempt(Attempt $attempt)
    {
        $this->attempts[] = $attempt;
    }

    /**
     * @param Question $question
     */
    public function addQuestion(Question $question)
    {
        $this->questions[] = $question;
    }

    /**
     * @param User $author
     */
    public function assignAuthor(User $author)
    {
        $author->addTest($this);
        $this->author = $author;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getTimeLimit(): int
    {
        return $this->timeLimit;
    }

    /**
     * @param int $timeLimit
     */
    public function setTimeLimit(int $timeLimit)
    {
        $this->timeLimit = $timeLimit;
    }

    /**
     * @return string
     */
    public function getShowAnswers(): string
    {
        return $this->showAnswers;
    }

    /**
     * @param string $showAnswers
     */
    public function setShowAnswers(string $showAnswers)
    {
        $this->showAnswers = $showAnswers;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
    }

    /**
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

}

