<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Test
 *
 * @ORM\Table(name="tests")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TestRepository")
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
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Question",
     *     mappedBy="test",
     *     cascade={"persist"}
     * )
     * @ORM\OrderBy({"serialNumber" = "ASC"})
     */
    private $questions;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Заполните это поле")
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="Минимальная длина {{ limit }} символа",
     *     maxMessage="Максимальная длина {{ limit }} символов"
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     * @Assert\Length(
     *     max=65535,
     *     maxMessage="Максимальная длина описания 64 kb"
     * )
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="time_limit", type="integer", nullable=true)
     * @Assert\Type(
     *     type="integer",
     *     message="Введите целое число"
     * )
     * @Assert\Range(
     *     min=0,
     *     max=1000,
     *     minMessage="Значение не может быть меньше {{ limit }}",
     *     maxMessage="Значение не может быть больше {{ limit }}"
     * )
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
     * @ORM\Column(name="show_answers", type="string")
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
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
    public function getDescription()
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
    public function getTimeLimit()
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
    public function getShowAnswers()
    {
        return in_array($this->showAnswers, ["yes", "1"]);
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

    public function getMaxPoints()
    {
        $points = 0;

        foreach ($this->getQuestions() as $question) {
            $points += $question->getPrice();
        }

        return $points;
    }

    public function setShowAnswersString()
    {
        if ($this->getShowAnswers()) {
            $this->setShowAnswers(self::SHOW_ANSWERS);
        } else {
            $this->setShowAnswers(self::NOT_SHOW_ANSWERS);
        }
    }

    public function fixBrokenRelations()
    {
        foreach ($this->getQuestions() as $question) {
            $question->setTest($this);
            foreach ($question->getVariants() as $variant) {
                $variant->setQuestion($question);
                if ($variant->getIsCorrect()) {
                    $variant->setIsCorrect("yes");
                } else {
                    $variant->setIsCorrect("no");
                }
            }
        }
    }

}

