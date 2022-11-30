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

    // get the parameters as json from parameter file
    static function param_json ($index = 1, $default=[])
    {
        $result = $default;
        $file = cli::parameters($index);
        if ($file && file_exists(gaia::$root . "/$file")) {
            $file = gaia::$root . "/$file";
        }
        // debug
        echo "(file: $file)";
        // get the json data if file exists
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $result = json_decode($json, true);
            $result ??= $default;
        }

        // return the json data
        return $result;
    }

    static function cron ()
    {
        // get path root
        $path_root = gaia::kv("root");
        // set working directory
        chdir($path_root);
        // glob todos files
        $path_todos = gaia::kv("path_data") . "/cron/todos";
        $path_done = gaia::kv("path_data") . "/cron/done";
        // create the done folder if it does not exist
        if (!file_exists($path_done)) {
            mkdir($path_done, 0777, true);
        }

        $files = glob("$path_todos/*.md");
        // find the most old file
        $file = null;
        $time = time();
        // print_r($files);
        foreach ($files as $f) {
            $t = filemtime($f);
            if ($t < $time) {
                $time = $t;
                $file = $f;
            }
        }
        // if file exists then execute it
        if ($file) {
            // get the command
            $cmd = file_get_contents($file);
            $cmd = trim($cmd);
            echo "(cmd: $cmd)";

            // avoid blocking on long running process
            // if file name starts with 0- then update date
            // else move the file to done folder
            if (substr(basename($file), 0, 2) == "0-") {
                touch($file);
            } else {
                // move the file to done
                $file_done = "$path_done/" . basename($file);
                rename($file, $file_done);
            }

            // execute the command
            if ($cmd) {
                ob_start();

                // execute the command
                $res = shell_exec($cmd);
                $res ??= "...";

                // get the output
                $out = ob_get_clean();
                $time = time();
                // append to daily log file done/cron-ymd.log
                $ymd = date("ymd", $time);
                $date = date("Y-m-d H:i:s", $time);
                $path_log = "$path_done/cron-$ymd.log";
                $log = "($date) $cmd\n$res\n$out\n";
                file_put_contents($path_log, $log, FILE_APPEND);
            }
        }
    }
}