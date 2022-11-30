<?php 

// POO code organisation
class gaia
{
    static $root = __DIR__;
    static $class_glob = [];

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
        static::$class_glob[] = __DIR__ . "/class";
        static::$class_glob[] = __DIR__ . "/class/*";

        foreach(static::$class_glob as $path)
        {
            $glob_search = "$path/$class.php";
            $files = glob($glob_search);
            // pick the first file
            $found = $files[0] ?? "";
            if ($found) {
                include $found;
                return;
            }
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