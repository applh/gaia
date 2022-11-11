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
    
    // filters
    static function filter ($type, $name, $default="")
    {
        $in = os::input($name, $default);
        if ($type= "var") {
            // remove special characters
            $in = preg_replace("/[^a-zA-Z0-9_]/", "", $in);
        }
        elseif ($type == "int")
        {
            $in = intval($in);
        }
        elseif ($type == "float")
        {
            $in = floatval($in);
        }

        return $in;
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
    //@end_class
}

//@end_file
