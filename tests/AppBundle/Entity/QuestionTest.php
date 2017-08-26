<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Question;
use AppBundle\Entity\Variant;
use PHPUnit\Framework\TestCase;

class QuestionTest extends TestCase
{
    public function testGetShuffledVariants()
    {
        $question = new Question();
        for ($i = 1; $i <= 4; $i++) {
            $variant = new Variant();
            $variant->setAnswer('variant'.$i);
            $question->addVariant($variant);
        }

        $resultSet = [];
        for ($i = 1; $i <= 10; $i++) {
            $variants = $question->getShuffledVariants();
            if (!in_array($variants, $resultSet)) {
                $resultSet[] = $variants;
            }
        }

        $this->assertGreaterThan(1, count($resultSet));
    }

    public function testGetCorrectVariants()
    {
        $question = new Question();
        for ($i = 1; $i <= 4; $i++) {
            $variant = new Variant();
            $isCorrect = ($i % 2 === 0)
                ? Variant::VARIANT_CORRECT
                : Variant::VARIANT_WRONG;
            $variant->setIsCorrect( $isCorrect );
            $variant->setAnswer('variant'.$i);
            $question->addVariant($variant);
        }

        $correctVariants = $question->getCorrectVariants();
        $this->assertEquals(2, count($correctVariants));
    }

    public function testGetVariantsList()
    {
        $question = new Question();
        for ($i = 1; $i <= 4; $i++) {
            $variant = new Variant();
            $variant->setAnswer('variant'.$i);
            $question->addVariant($variant);
        }

        $variantsList = $question->getVariantsList();

        $this->assertTrue(is_array($variantsList));
        $this->assertEquals(4, count($variantsList));
    }

}
