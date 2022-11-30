<?php

/**
 * class: route_default
 * creation: 2022-11-30 15:01:42
 * author: AppLH.com
 * license: MIT
 */


class route_default
{
    //@start_class

    /**
     * this route should handle the urls without folders
     * homepage, main pages, robots.txt, ...
     */
    static function check ($dir1, $filename="", $extension="")
    {
        $template = "404.php";
        
        return $template;
    }

    //@end_class
}

//@end_file
