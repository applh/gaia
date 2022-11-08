<?php 

// POO code organisation
class gaia
{
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
        // load the config file config.php if it exists
        if (file_exists(__DIR__ . "/my-data/config.php")) {
            require __DIR__ . "/my-data/config.php";
        }

        // install class autoloader
        spl_autoload_register('gaia::autoload');

        // install composer autoloader
        require __DIR__ . "/vendor/autoload.php";
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
}

// call the web method
gaia::main();