<?php

namespace AppBundle\Helper;

class Result
{
    /**
     * @var int $points
     */
    private $points;

    /**
     * @var int $rightAnswersCount
     */
    private $rightAnswersCount;

    /**
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param int $points
     */
    public function setPoints(int $points)
    {
        $this->points = $points;
    }

    /**
     * @return int
     */
    public function getRightAnswersCount()
    {
        return $this->rightAnswersCount;
    }

    /**
     * @param int $rightAnswersCount
     */
    public function setRightAnswersCount(int $rightAnswersCount)
    {
        $this->rightAnswersCount = $rightAnswersCount;
    }
}
