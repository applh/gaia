<?php 

// POO code organisation
class index
{
    static function web ()
    {
        require __DIR__ . "/../gaia.php";
    }
}

// call the web method
index::web();