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

        // read the code from sample.php in the same folder
        $code = file_get_contents(__DIR__ . "/code_sample.php");
        // replace the placeholders
        $code = str_replace("code_sample", $classname, $code);
        $code = str_replace("==DATE==", date("Y-m-d H:i:s"), $code);
        $code = str_replace("==AUTHOR==", gaia::kv("code_author") ?? "YOUR NAME", $code);
        $code = str_replace("==LICENSE==", gaia::kv("code_license") ?? "YOUR LICENSE", $code);

        // write the code to YOURCLASS.php
        file_put_contents($classfile, $code);

    }

    static function md5 ()
    {
        // get parameter 2
        $source = cli::parameters(2);
        // md5 the source
        $md5 = md5($source);
        // print the md5
        echo "($source)($md5)".PHP_EOL;
    }

    static function sha1 ()
    {
        // get parameter 2
        $source = cli::parameters(2);
        // sha1 the source
        $sha1 = sha1($source);
        // print the md5
        echo "($source)($sha1)".PHP_EOL;
    }

    static function api_key ()
    {
        // build a valid api_key
        // get the api key from the config file
        $key_private = gaia::kv("api/key") ?? cli::parameters(2) ?? "";
        // get the maxtime
        $maxtime = time() + 3600;
        // build the api_key
        $keys = [
            "maxtime" => $maxtime, 
            "hash" => password_hash($key_private . $maxtime, PASSWORD_DEFAULT)
        ];
        $api_key = json_encode($keys);
        // encode the api_key
        // complete the api_key so base64 has no special characters
        $key_length = mb_strlen($api_key);
        $missing = 4 - $key_length % 4;
        $api_key = str_pad($api_key, $key_length + $missing);

        $api_key = base64_encode($api_key);
        // print the api_key
        print_r($keys);
        echo 
        <<<txt
        
        (private_key: $key_private)
        (api_key: $api_key)

        php gaia.php code::check_key $api_key

        txt;
    }

    static function check_key ()
    {
        // get parameter 2
        $param64 = cli::parameters(2);
        $key_private = cli::parameters(3) ?? gaia::kv("api/key") ?? "";

        // if $api_key is empty, then exit
        if (empty($param64))
        {
            echo "Usage: php gaia.php code::check_key YOURAPIKEY".PHP_EOL;
            return;
        }
        // decode the api_key
        $api_key = base64_decode($param64);
        // trim api_key
        $api_key = trim($api_key);
        // api_key is json encoded
        $keys = json_decode($api_key, true);
        // check if $keys is valid
        if (is_array($keys))
        {
            $maxtime = $keys["maxtime"] ?? 0;
            $hash = $keys["hash"] ?? "";
            // check if $maxtime is not expired
            if ($maxtime > time())
            {
                // check if $hash is password_hash($key_private . $maxtime)
                if (password_verify($key_private . $maxtime, $hash))
                {
                    print_r($keys);
                    echo "The api_key is valid".PHP_EOL;
                }
                else
                {
                    echo "The api_key is not valid ($key_private)".PHP_EOL;
                    print_r($keys);
                }
            }
            else
            {
                echo "The api_key is expired".PHP_EOL;
            }
        }
        else
        {
            echo "WTF api_key is not valid ($param64)($api_key)".PHP_EOL;
        }
    }

    static function toto ()
    {
        // test api key hash
        $k = "7ea80b6f5c7a03af89ca7042689cb8e12d615ec11669918634";
        $p = "7ea80b6f5c7a03af89ca7042689cb8e12d615ec1";
        $t = "1669919513";

        $h = '$2y$10$ygzib2mhCuIdStAQmEjjRuJa503DbNNkyIB/RomI9mCNpiwE9NWty';
        var_dump(password_verify("$p$t", $h));

    }
    //@end_class
}

//@end_file
