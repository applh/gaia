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

    static function markdown ()
    {
        // $file = gaia::kv("path_data") . "/pages/project-blog.md";
        $file = gaia::kv("os/markdown/file") ?? "";
        $file = gaia::kv("path_data") . "/$file";

        // if file exists then read it
        if ($file && file_exists($file))
        {
            $lines = file($file);
            $titles = [];
            $blocs = [];

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
            print_r($titles);

            gaia::kv("os/markdown/titles", $titles);
            gaia::kv("os/markdown/blocs", $blocs);

        }
    }

    //@end_class
}

//@end_file