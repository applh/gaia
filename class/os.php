<?php

/**
 * class: os
 * creation: 2022-11-09 11:58:08
 * author: applh.com
 * license: MIT
 */


class os
{
    //@start_class
    static $logs = [];

    static function markdown ($mdfile = null)
    {
        // $file = gaia::kv("path_data") . "/pages/project-blog.md";
        $file = $mdfile ?? gaia::kv("os/markdown/file") ?? "";
        $file = gaia::kv("path_data") . "/$file";

        $blocs = [];

        // if file exists then read it
        if ($file && file_exists($file))
        {
            $lines = file($file);
            $titles = [];

            // loop each line and select lines starting with #
            $last_title = 0;
            foreach($lines as $index => $line)
            {
                if (substr($line, 0, 1) == "#")
                {
                    // echo $line;
                    // if line starts with "# " then add in $titles["h1"]
                    if (substr($line, 0, 2) == "# ")
                    {
                        $titles["h1"]["$index"] = $line;
                    }
                    // if line starts with "## " then add in $titles["h2"]
                    if (substr($line, 0, 3) == "## ")
                    {
                        $titles["h2"]["$index"] = $line;
                    }
                    // if line starts with "### " then add in $titles["h3"]
                    if (substr($line, 0, 4) == "### ")
                    {
                        $titles["h3"]["$index"] = $line;
                    }

                    // build the content from the last title to the current title
                    $blocs["$last_title"]["content"] = implode("", array_slice($lines, $last_title+1, $index - $last_title -1));

                    $blocs["$index"] = [ 
                        "title" => $line,
                    ];
                    // update last title index
                    $last_title = $index;
                }
            }
            // build the content from the last title to the end of the file
            $blocs["$last_title"]["content"] = implode("", array_slice($lines, $last_title+1));
            
            // debug
            // print_r($titles);

            gaia::kv("os/markdown/titles", $titles);
            gaia::kv("os/markdown/blocs", $blocs);

        }

        return $blocs;
    }


    // get request parameters
    static function input ($name, $default="")
    {
        if ($name) {
            // get the value from the request
            $value = $_REQUEST[$name] ?? $default;
        }
        else {
            $value = $default;
        }
        // return the value
        return $value;
    }
    

    static function debug ($var=null)
    {
        if ($var) {
            static::$logs[] = $var;
        }
        else {
            return static::$logs;
        }
    }

    static function debug_headers ()
    {
        foreach(static::$logs as $index => $log)
        {
            header("X-Debug-$index: $log");
        }
    }

    static function cache ($key, $value=null, $ttl=3600)
    {
        static $path_cache = null;
        static $now = null;

        if ($path_cache == null) {
            $now = time();
            // path domain
            $path_domain = gaia::kv("path_domain");
            $path_cache = "$path_domain/cache";
            // os::debug("cache($path_cache)($key, $ttl)");
            // create cache folder if not exists
            if (!is_dir($path_cache)) {
                mkdir($path_cache, 0777, true);
                chmod($path_cache, 0777);
            }
        }
        // cache file
        // md5($key) to avoid special characters in the filename
        $key_md5 = md5($key);
        $file = "$path_cache/tmp-$key_md5";
        // if value is null then read the cache file
        if ($value === null) {
            // check ttl
            if (file_exists($file)) {
                if ($now < $ttl + filemtime($file)) {
                    $value = true;
                    gaia::kv("os/cache/file", $file);
                }
                else {
                    // WARNING: can be dangerous
                    // delete the cache file if exists
                    unlink($file);
                }
            }
            else {
                // os::debug("cache_not_found($key_md5)($file)");
            }
        }
        // if value is not null then write the cache file
        else {
            os::debug("cache_write($key)($file)");
            // write the cache file
            file_put_contents($file, $value);
            chmod($file, 0666);
        }

        return $value;
    }

    //@end_class
}

//@end_file
