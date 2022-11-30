<?php

/**
 * class: route_media
 * creation: 2022-11-28 17:46:04
 * author: AppLH.com
 * license: MIT
 */


class route_media
{
    //@start_class

    static function check ($dir1, $filename="", $extension="")
    {
        $template = "404.php";

        // path domain
        $path_domain = gaia::kv("path_domain");
        $path_media = "$path_domain/media/$dir1.$extension";
        os::debug("($dir1, $filename, $extension)$path_media");
        // if file exists then reead it
        if (is_file($path_media)) {
            $mime = web::mime($extension);
            gaia::kv("headers/Content-Type", $mime);
            header("Content-Type: $mime");
            // cache control
            header("Cache-Control: max-age=31536000");

            readfile($path_media);

            $template = "";
        }
        
        return $template;
    }

    //@end_class
}

//@end_file
