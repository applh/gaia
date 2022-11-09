<?php

/**
 * class: code
 * creation: ==DATE==
 * author: ==AUTHOR==
 * license: ==LICENSE==
 */


class code
{
    //@start_class

    static function write ()
    {
        // get the class name from parameter 2
        $classname = cli::parameters(2);
        // if $classname is empty, then exit
        if (empty($classname))
        {
            echo "Usage: php gaia.php code::write YOURCLASS".PHP_EOL;
            return;
        }
        
        // debug
        echo "classname: $classname".PHP_EOL;

        // get the class file path
        $classfile = gaia::kv("path_class") . "/$classname.php";
        // if the class file exist, then return
        if (file_exists($classfile))
        {
            echo "The class file already exist: $classfile".PHP_EOL;
            return;
        }

        // read the code from sample.php
        $code = file_get_contents("class/sample.php");
        // replace the placeholders
        $code = str_replace("sample", $classname, $code);
        $code = str_replace("==DATE==", date("Y-m-d H:i:s"), $code);
        $code = str_replace("==AUTHOR==", gaia::kv("code_author") ?? "YOUR NAME", $code);
        $code = str_replace("==LICENSE==", gaia::kv("code_license") ?? "YOUR LICENSE", $code);

        // write the code to YOURCLASS.php
        file_put_contents($classfile, $code);

    }

    //@end_class
}

//@end_file
