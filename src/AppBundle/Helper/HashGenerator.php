<?php

namespace AppBundle\Helper;

class HashGenerator
{
    const HASH_LENGTH = 40;

    public static function generateHash()
    {
        $symbols = self::getSymbols();

        $hash = '';
        for ($i = 0; $i < 40; $i++) {
            $hash .= $symbols[array_rand($symbols)];
        }

        return sha1($hash);
    }

    private static function getSymbols()
    {
        return [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u',
            'v', 'w', 'x', 'y', 'z', '~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '+', '=', '[', ']',
            '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '<', '>', '.', ',', '?', ':', ';', '{', '}',
        ];
    }
}
