<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Variant
 *
 * @ORM\Table(name="variants")
 * @ORM\Entity
 */
class Variant
{
    const VARIANT_CORRECT = 'yes';
    const VARIANT_WRONG = 'no';

    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="string", length=255, nullable=false)
     */
    private $answer;

    /**
     * @var string
     *
     * @ORM\Column(name="is_correct", type="string")
     */
    private $isCorrect;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Question
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Question",
     *     inversedBy="variants"
     * )
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;

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
    public function setQuestion(Question $question = null)
    {
        if ($question !== null) {
            $question->addVariant($this);
        }

        $this->question = $question;
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
    public function getAnswer()
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
     * @return string
     */
    public function getIsCorrect()
    {
        return in_array($this->isCorrect, ["yes", "1"]);
    }

    /**
     * @param string $isCorrect
     */
    public function setIsCorrect(string $isCorrect)
    {
        $this->isCorrect = $isCorrect;
    }

}

