<?php

namespace App\Classes;

class FirstNameLastName
{
    public static function run($fullname)
    {
        $fullname = explode(" ", $fullname);
        $first_name = $fullname[0];
        $last_name = count($fullname) > 1 ? $fullname[1] : "";

        return [$first_name, $last_name];
    }
}
