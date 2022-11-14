<?php

/**
 * class: api_git
 * creation: 2022-11-14 12:03:52
 * author: YOUR NAME
 * license: YOUR LICENSE
 */


class api_git
{
    //@start_class

    static function pull ()
    {
        // get the path to the git repository
        $path_root = gaia::kv("root");
        // set working directory
        chdir($path_root);
        // command line
        $cmd = "git pull";
        // execute command line
        $res = shell_exec($cmd) ?? "...";
        // return result
        return $res;
    }

    //@end_class
}

//@end_file
