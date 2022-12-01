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

    static function form_infos ($name)
    {
        $form_infos = [];

        // load form infos from json file in site templates folder
        $path_domain = gaia::kv("path_domain");
        $path_form = "$path_domain/templates/form-$name.json";
        // if file exists then load it
        if (is_file($path_form)) {
            $form_infos = json_decode(file_get_contents($path_form), true);
        }

        return $form_infos;
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
        foreach ($ainputs as $index => $inputs) {
            extract($inputs);
            $name ??="";
            $type ??= "text";
            if ($name) {
                $value = form::filter_input($type, $name);
                // store the input for later use
                static::$inputs[$name] = $value;
            }
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

    static function js ($names)
    {
        $infos = [];
        foreach($names as $name) {
            $infos[$name] = form::form_infos($name);
        }
        $js = json_encode($infos, JSON_PRETTY_PRINT);

        echo $js;
    }

    static function process ($form_name)
    {
        $form_infos = form::form_infos($form_name);

        if (!empty($form_infos)) {
            // get name, email, message
            // check form
            form::filters($form_infos["fields"] ?? []);
            // debug
            api::json_data("form_infos", $form_infos);
            if (form::is_ok()) {
                // check if form process exists
                $callback = $form_infos["process_form"] ?? "";
                if ($callback && is_callable($callback)) {
                    $message = $callback($form_infos);
                }
            } else {
                $message = implode(", ", form::$errors);
                $now = form::now("d/m/y H:i:s");
                gaia::kv("api/feedback", "$message ($now)");
            }
        }

    }

    static function now ($format = "Y-m-d H:i:s")
    {
        static $time = null;
        
        $time ??= time();
        return date($format, $time);
    }

    static function process_list_forms ()
    {
        $forms = [];
        // get names
        extract(form::$inputs);
        $names ??= "";
        $names = explode(",", $names);
        foreach ($names as $name) {
            // trim name
            $name = trim($name);
            // sanitize name
            $name = preg_replace("/[^a-zA-Z0-9-_]/", "", $name);
            // check if form exists
            $form_infos = form::form_infos($name);
            if (!empty($form_infos)) {
                $forms[$name] = $form_infos;
            }
        }

        api::json_data("forms", $forms);
    }

    static function process_mail ($form_infos)
    {
        $form_name = $form_infos["name"] ?? "";
        $form_feedback = $form_infos["feedback"] ?? "";

        $now = form::now("d/m/y H:i:s");
        $dtnow = form::now("Y-m-d H:i:s");
        $fnow = form::now("Ymd-His");

        extract(form::$inputs);
        $name ??= "";
        $email ??= "";
        $message ??= "";

        // send email
        $subject = "($form_name) from $name";
        $body =
        <<<txt
        ($form_name) at $now
        
        $name 
        $email

        $message

        txt;

        $to = gaia::kv("admin/email") ?? "";

        if ($to) {
            mailer::send($to, $subject, $body);
        }

        // create new line in sqlite db
        model::create([
            "path" => "forms/$form_name",
            "filename" => "$form_name-$fnow",
            "title" => $subject,
            "content" => $body,
            "created" => $dtnow,
        ]);

        // add feedback
        gaia::kv("api/feedback", "$form_feedback ($now)");

    }

    //@end_class
}

//@end_file
