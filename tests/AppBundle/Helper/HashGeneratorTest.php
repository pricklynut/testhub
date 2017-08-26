<?php

namespace Tests\AppBundle\Helper;

use AppBundle\Helper\HashGenerator;
use PHPUnit\Framework\TestCase;

class HashGeneratorTest extends TestCase
{
    public function testGenerateHash()
    {
        $hash = HashGenerator::generateHash();

        $this->assertTrue(is_string($hash));
        $this->assertEquals(HashGenerator::HASH_LENGTH, strlen($hash));
    }

}
