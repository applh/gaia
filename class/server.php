<?php

/**
 * class: server
 * creation: 2022-11-10 10:51:18
 * author: applh.com
 * license: MIT
 */


class server
{
    //@start_class

    static function run ()
    {
        // start PHP server
        // https://www.php.net/manual/en/features.commandline.webserver.php
        // example:
        // php gaia.php server::run

        // $cmd = "php -S localhost:8000";
        // $cmd = "php -S localhost:8000 -t /var/www/html/gaia";
        // $cmd = "php -S localhost:8000 -t /var/www/html/gaia -d display_errors=1";
        // $cmd = "php -S localhost:8000 -t /var/www/html/gaia -d display_errors=1 -d error_reporting=E_ALL";
        // $cmd = "php -S localhost:8000 -t /var/www/html/gaia -d display_errors=1 -d error_reporting=E_ALL -d error_log=/var/www/html/gaia/my-data/error.log";
        // $cmd = "php -S localhost:8000 -t /var/www/html/gaia -d display_errors=1 -d error_reporting=E_ALL -d error_log=/var/www/html/gaia/my-data/error.log -d log_errors=1";
        // $cmd = "php -S localhost:8000 -t /var/www/html/gaia -d display_errors=1 -d error_reporting=E_ALL -d error_log=/var/www/html/gaia/my-data/error.log -d log_errors=1 -d error_prepend_string='[gaia] '";
        // $cmd = "php -S localhost:8000 -t /var/www/html/gaia -d display_errors=1 -d error_reporting=E_ALL -d error_log=/var/www/html/gaia/my-data/error.log -d log_errors=1 -d error_prepend_string='[gaia] ' -d error_append_string='";

        // get path root
        $path_root = gaia::kv("root");    
        // PHP local server by router script    
        $cmd = "php -S localhost:8000 $path_root/public/index.php";

        // launch shell command
        $output = shell_exec($cmd);
    }

    //@end_class
}

//@end_file
