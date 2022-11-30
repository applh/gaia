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
        // NOT WORKING... wordops blocks the command line ???
        // touch my-data/cron/todos/git-pull.md
        $path_data = gaia::kv("path_data");
        $path_todos = "$path_data/cron/todos";
        // create the folder if it does not exist
        if (!file_exists($path_todos)) {
            mkdir($path_todos, 0777, true);
        }
        file_put_contents("$path_todos/git-pull.md", "git -v pull");
        // chmod 666 my-data/cron/todos/git-pull.md
        chmod("$path_todos/git-pull.md", 0666);

        // return result
        return $res;
    }

    static function status ()
    {
        // get the path to the git repository
        $path_root = gaia::kv("root");
        // set working directory
        chdir($path_root);
        // command line
        $cmd = "git status";
        // execute command line
        $res = shell_exec($cmd) ?? "...";
        // return result
        return $res;
    }

    //@end_class
}

//@end_file
