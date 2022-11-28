<?php

/**
 * class: site
 * creation: 2022-11-28 14:09:57
 * author: YOUR NAME
 * license: YOUR LICENSE
 */


class site
{
    //@start_class

    static function setup ()
    {
        // get domain name
        $domain = $_SERVER["HTTP_HOST"];
        // sanitize domain name
        // replace all non-alphanumeric characters with a dash
        // - and . are ok
        $domain = preg_replace("/[^a-zA-Z0-9-\.]/", "-", $domain);
        // get the domain config file
        os::debug("$domain");
        $path_data = gaia::kv("path_data");

        $path_domain = "$path_data/sites/$domain";
        $path_config = "$path_domain/config.php";

        gaia::kv("domain", $domain);
        gaia::kv("path_domain", $path_domain);

        // load the config file config.php if it exists
        if (file_exists($path_config)) {
            require $path_config;
        }
    }

    static function template ($filename, $template)
    {
        $templateFile = null;
        // get domain name
        $path_domain= gaia::kv("path_domain");
        $path_template = "$path_domain/templates/$template";
        os::debug("($filename, $template)$path_template");
        // check if template exists
        if (file_exists($path_template)) {
            $templateFile = $path_template;
            include($templateFile);
        }
        else {
            $path_root = gaia::kv("root");
            $templateFile = "$path_root/templates/$template";
            os::debug("($filename, $template)$templateFile");
            if (is_file($templateFile)) {
                include($templateFile);
            }
        }

        return $templateFile;
    }

    //@end_class
}

//@end_file
