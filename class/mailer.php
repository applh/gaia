<?php

/**
 * class: mailer
 * creation: 2022-11-30 15:22:24
 * author: AppLH.com
 * license: MIT
 */


class mailer
{
    //@start_class

    static function send ($to, $subject, $message, $headers=[])
    {
        // add from if not present in $headers
        $headers["From"] ??= gaia::kv("site/email/from") ?? "";

        $res = @mail($to, $subject, $message, $headers);
        return $res;
    }

    //@end_class
}

//@end_file
