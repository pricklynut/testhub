<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Question
 *
 * @ORM\Table(name="questions")
 * @ORM\Entity
 */
class Question
{
    const TYPE_STRING_TYPEIN = 'string_typein';

    const TYPE_NUMBER_TYPEIN = 'number_typein';

    const TYPE_SINGLE_VARIANT = 'single_variant';

    const TYPE_MULTIPLE_VARIANTS = 'multiple_variants';

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="text", length=65535, nullable=false)
     */
    private $question;

    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="integer", nullable=false)
     */
    private $price = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type = self::TYPE_STRING_TYPEIN;

    /**
     * @var integer
     *
     * @ORM\Column(name="serial_number", type="integer", nullable=false)
     */
    private $serialNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="`precision`", type="integer", nullable=true)
     */
    private $precision = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Test
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Test",
     *     inversedBy="questions"
     * )
     * @ORM\JoinColumn(name="test_id", referencedColumnName="id")
     */
    private $test;

    /**
     * @var Collection
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Answer",
     *     mappedBy="question"
     * )
     */
    private $answers;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Variant",
     *     mappedBy="question",
     *     cascade={"persist"}
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $variants;

    /**
     * Question constructor.
     */
    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->variants = new ArrayCollection();
    }

    /**
     * @param Answer $answer
     */
    public function addAnswer(Answer $answer)
    {
        $this->answers[] = $answer;
    }

    /**
     * @param $variants
     */
    public function setVariants($variants)
    {
        $this->variants = $variants;
    }

    /**
     * @param Variant $variant
     */
    public function addVariant(Variant $variant)
    {
        $this->variants[] = $variant;
    }

    public function removeVariant(Variant $variant)
    {
        $this->variants->removeElement($variant);
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
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion(string $question)
    {
        $this->question = $question;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(int $price)
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * @param int $serialNumber
     */
    public function setSerialNumber(int $serialNumber)
    {
        $this->serialNumber = $serialNumber;
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
    public function setTest(Test $test = null)
    {
        if ($test !== null) {
            $test->addQuestion($this);
        }

        $this->test = $test;
    }

    /**
     * @return Collection
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    /**
     * @return Collection
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * @return array
     */
    public function getShuffledVariants()
    {
        $items = $this->variants->getValues();

        shuffle($items);

        return $items;
    }

    /**
     * @return array
     */
    public function getCorrectVariants()
    {
        $correctVariants = [];

        foreach ($this->getVariants() as $variant) {
            if ($variant->getIsCorrect()) {
                $correctVariants[] = $variant;
            }
        }

        return $correctVariants;
    }

    /**
     * @return array
     */
    public function getVariantsList()
    {
        $list = [];
        $models = $this->getShuffledVariants();

        foreach ($models as $model) {
            $list[$model->getAnswer()] = $model->getAnswer();
        }

        return $list;
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @param int $precision
     */
    public function setPrecision(int $precision)
    {
        $this->precision = $precision;
    }

}

