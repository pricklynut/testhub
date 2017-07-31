<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Answer
 *
 * @ORM\Table(name="answers")
 * @ORM\Entity
 */
class Answer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="string", length=255, nullable=false)
     */
    private $answer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="received", type="datetime", nullable=false)
     */
    private $received;

    /**
     * @var \AppBundle\Entity\Attempt
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Attempt",
     *     inversedBy="answers"
     * )
     * @ORM\JoinColumn(name="attempt_id", referencedColumnName="id")
     */
    private $attempt;

    /**
     * @var \AppBundle\Entity\Question
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Question",
     *     inversedBy="answers"
     * )
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;

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
    public function getAnswer(): string
    {
        return $this->answer;
    }

    /**
     * @param string $answer
     */
    public function setAnswer(string $answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return \DateTime
     */
    public function getReceived(): \DateTime
    {
        return $this->received;
    }

    /**
     * @param \DateTime $received
     */
    public function setReceived(\DateTime $received)
    {
        $this->received = $received;
    }

    /**
     * @return Attempt
     */
    public function getAttempt(): Attempt
    {
        return $this->attempt;
    }

    /**
     * @param Attempt $attempt
     */
    public function setAttempt(Attempt $attempt)
    {
        $attempt->addAnswer($this);
        $this->attempt = $attempt;
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @param Question $question
     */
    public function setQuestion(Question $question)
    {
        $question->addAnswer($this);
        $this->question = $question;
    }


}

