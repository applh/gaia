<?php

/**
 * class: route_api
 * creation: 2022-11-30 14:42:19
 * author: AppLH.com
 * license: MIT
 */


class route_api
{
    //@start_class

    static function check ($dir1, $filename="", $extension="")
    {
        $template = "404.php";
        if ($filename == "json") {
            api::json();
            $template = "";
        }
        
        return $template;
    }

    //@end_class
}

//@end_file
