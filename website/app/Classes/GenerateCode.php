<?php

namespace App\Classes;

class GenerateCode
{
    public static function run($length = null, $smallChar = null)
    {
        $length = $length ? $length : 8;
        $smallChar = $smallChar ? $smallChar : false;
        $code = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        if ($smallChar) {
            $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        }
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $code .= $codeAlphabet[rand(0, $max - 1)];
        }

        return $code;
    }
}
