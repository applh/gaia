<?php

class cli
{
    // run the cli application
    static function run ()
    {
        // get parameter 1
        $p2 = cli::parameters(1);
        // debug
        echo "(p2: $p2)";

        // very easy cli application
        // just call the static method of the class
        // example: 
        // php gaia.php test::hello

        // if p2 is callable then call it
        if ($p2 && is_callable($p2)) {
            $p2();
        }

    }

    // get the parameters
    static function parameters ($index = 1, $default="")
    {
        // get the parameters
        static $parameters = null;
        $parameters ??= $_SERVER["argv"];

        // return the parameter
        return $parameters[$index] ?? $default;
    }
}