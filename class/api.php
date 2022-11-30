<?php

/**
 * class: api
 * creation: 2022-11-30 14:34:29
 * author: AppLH.com
 * license: MIT
 */


class api
{
    //@start_class
    static $data = [];

    /**
     * store key/value name/data in api::$data
     */
    static function json_data ($name, $value) {
        static::$data[$name] = $value;
    }

    static function json()
    {
        $infos = [];

        // add date to infos
        $infos["now"] = date("ymd-His");
        // add $_REQUEST to infos
        $infos["request"] = $_REQUEST;
        // add $_FILES to infos
        $infos["files"] = $_FILES;

        // check if api call
        $c = form::filter("var", "c", "public");
        $m = form::filter("var", "m");

        if ($c && $m) {
            $api_call = "api_$c::$m";
            // if api call then call the api
            if (is_callable($api_call)) {
                // check controller
                $api_access = false;
                $api_control = "controller::$c";
                if (is_callable($api_control)) {
                    // call controller
                    $api_access = $api_control($m);
                }

                // if access granted then call the api
                if ($api_access) {
                    $infos["api_call"] = $api_call;
                    $infos["api_result"] = $api_call();
                } else {
                    // if access denied then return 403
                    // header("HTTP/1.0 403 Forbidden");
                    // echo "403 Forbidden";
                }
            }
        }
        $infos["feedback"] = gaia::kv("api/feedback") ?? "";
        $infos["data"] = api::$data;
        
        // add debug infos
        $infos["debug"] = os::debug();

        // return json data
        header("Content-Type: application/json");
        echo json_encode($infos, JSON_PRETTY_PRINT);
    }

    //@end_class
}

//@end_file
