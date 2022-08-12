<?php

namespace Application\Core;

class RandomGenerator
{
    public static function generateRandonString(int $minLength, ?int $maxLength = null): string
    {
        $length = ($maxLength === null) ? $minLength : random_int($minLength, $maxLength);
        $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return $string;
    }
    public static function generateRandomCode(int $minLength, ?int $maxLength = null): string
    {
        $length = ($maxLength === null) ? $minLength : random_int($minLength, $maxLength);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= random_int(0, 9);
        }
        return $code;
    }
}
