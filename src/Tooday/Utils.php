<?php

namespace Tooday;

class Utils
{
    public static function containsOneOf($string, array $words)
    {
        $string = str_replace(['.', ',', ':'], '' , $string);
        $strings = array_map('mb_strtolower', explode(' ', $string));
        $intersect = array_intersect($strings, $words);

        if (count($intersect) != 1) {
            return null;
        }

        return array_pop($intersect);
    }
}

