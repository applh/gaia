<?php

/**
 * class: api_public
 * creation: 2022-11-10 18:25:40
 * author: applh.com
 * license: MIT
 */


class api_public
{
    //@start_class

    static function test()
    {
        return "(test)";
    }

    static function stat()
    {
        $message = os::input("message");
        return $message;
    }

    static function list_forms()
    {
        $message = form::process("list-forms");
        return $message;
    }

    static function newsletter()
    {
        $message = form::process("newsletter");
        return $message;
    }

    static function contact()
    {
        $message = form::process("contact");
        return $message;
    }

    //@end_class
}

//@end_file
