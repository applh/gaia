<?php

/**
 * class: route_show
 * creation: 2022-11-10 21:09:10
 * author: applh.com
 * license: MIT
 */


class route_show
{
    //@start_class

    static function check ($dir1, $filename="", $extension="")
    {
        // echo "route_show::check($dir1)";
        os::debug($dir1);
        $template = "404.php";

        if ($dir1 == "media") {
            // check if media exists
            $path_media = gaia::kv("path_data") . "/pages/media/$filename.$extension";
            if (is_file($path_media)) {
                gaia::kv("web/media", $path_media);
                gaia::kv("web/media/extension", $extension);
                $template = "media.php";
            }

        }
        else {
            // check if dir1 is a file
            // replace _ by -
            $mdfile = str_replace("_", "-", $dir1);
            $file = gaia::kv("path_data") . "/pages/$mdfile.md";

            if (is_file($file))
            {
                gaia::kv("web/slides", "pages/$mdfile.md");
                $template = "revealjs.php";
            }
        }

        return $template;
    }

    //@end_class
}

//@end_file
