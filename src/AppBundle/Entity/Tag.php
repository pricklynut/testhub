<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity
 */
class Tag
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Test", inversedBy="tags")
     * @ORM\JoinTable(name="test_to_tag")
     */
    private $tests;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->tests = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getTests()
    {
        return $this->tests;
    }

    /**
     * @param Test $test
     */
    public function attachToTest(Test $test)
    {
        $this->tests[] = $test;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }


}

