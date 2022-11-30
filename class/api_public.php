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

    static function test ()
    {
        return "(test)";
    }

    static function stat ()
    {
        $message = os::input("message");
        return "(stat) $message";
    }

    static function contact ()
    {
        $now = date("d/m/y H:i:s");

        // load form infos from json file in site templates folder
        $path_domain = gaia::kv("path_domain");
        $path_form = "$path_domain/templates/contact.json";
        $form_infos = json_decode(file_get_contents($path_form), true);

        // get name, email, message
        // check form
        form::filters([
            "name" => [
                "type" => "text",
            ],
            "email" => [
                "type" => "email",
            ],
            "message" => [
                "type" => "text",
            ],
        ]);
        if (form::is_ok())
        {
            extract(form::$inputs);

            // send email
            $subject = "Contact from $name";
            $body = 
            <<<txt
            Contact from $name ($email) 
            at $now
            
            $message

            txt;

            $to = gaia::kv("admin/email") ?? "";
            $from = gaia::kv("site/email/from") ?? "";

            if ($from && $to) {
                $headers = "From: $from";
                mail($to, $subject, $body, $headers);
            }
        }
        else {
            $message = implode(", ", form::$errors);
        }

        gaia::kv("api/feedback", "$message ($now)");
        return "$message";
    }
    //@end_class
}

//@end_file
