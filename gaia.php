<?php 

// POO code organisation
class gaia
{
    static $root = __DIR__;
    
    static function main ()
    {
        // debug line
        // echo "(gaia main method)";
        gaia::setup();

        // web mode or cli mode
        if (is_callable("index::web")) {
            // web mode
            web::run();
        }
        else {
            // cli mode
            cli::run();
        }
    }

    static function setup ()
    {
        // setup paths
        gaia::kv("root", __DIR__);
        gaia::kv("path_class", __DIR__ . "/class");
        gaia::kv("path_media", __DIR__ . "/media");
        gaia::kv("path_data", __DIR__ . "/my-data");
        $path_config = gaia::kv("config", __DIR__ . "/my-data/config.php");

        // load the config file config.php if it exists
        if (file_exists($path_config)) {
            require $path_config;
        }

        // install class autoloader
        spl_autoload_register('gaia::autoload');

        // install composer autoloader
        // require if file exists
        $path_composer = __DIR__ .  "/vendor/autoload.php";
        if (file_exists($path_composer)) {
            require $path_composer;
        }
    }

    static function autoload ($class)
    {
        // basic autoloader
        // TODO: remove the namespace from the class name

        // get the class file
        $file = __DIR__ . "/class/$class.php";

        // check if the file exists
        if (file_exists($file)) {
            require $file;
        }
    }

    // get or set key values
    static function kv ($key, $value = null)
    {
        // get the key values
        static $kv = [];
        // if $value is null, then return the value
        if ($value === null) {
            return $kv[$key] ?? null;
        }
        // set the value
        $kv[$key] = $value;

        return $value;
    }
    
}

// call the web method
gaia::main();