<?php

/**
 * class: form
 * creation: 2022-11-30 11:53:07
 * author: AppLH.com
 * license: MIT
 */


class form
{
    //@start_class
    static $errors = [];
    static $inputs = [];

    static function is_ok()
    {
        return 0 == count(static::$errors);
    }

    /**
     * FIXME: is it a possible to merge filter and filter_input ?
     */
    static function filter ($type, $name, $default="")
    {
        $in = os::input($name, $default);
        if ($type == "var") {
            // remove special characters
            $in = preg_replace("/[^a-zA-Z0-9_]/", "", $in);
        } elseif ($type == "int") {
            $in = intval($in);
        } elseif ($type == "float") {
            $in = floatval($in);
        } elseif ($type == "email") {
            $email = filter_var($in, FILTER_VALIDATE_EMAIL);
            if ($email != $in) {
                $in = "";
            }
        }
        return $in;
    }

    static function filters($ainputs)
    {
        foreach ($ainputs as $name => $inputs) {
            extract($inputs);
            $type ??= "text";
            $value = form::filter_input($type, $name);
            $inputs[$name]["value"] = $value;
            // store the input for later use
            static::$inputs[$name] = $value;
        }
        return $inputs;
    }

    // filters
    static function filter_input($type, $name, $default = "", $required=true)
    {
        $in = os::input($name, $default);
        if ($type == "var") {
            // remove special characters
            $in = preg_replace("/[^a-zA-Z0-9_]/", "", $in);
        } elseif ($type == "int") {
            $in = intval($in);
        } elseif ($type == "float") {
            $in = floatval($in);
        } elseif ($type == "email") {
            $email = filter_var($in, FILTER_VALIDATE_EMAIL);
            if ($email != $in) {
                $in = "";
                static::$errors[] = "Invalid email";
            }
        }
        if ($required && !$in) {
            static::$errors[] = "Missing $name";
        }
        return $in;
    }

    //@end_class
}

//@end_file
