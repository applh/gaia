<?php

$infos = [];

// add date to infos
$infos["now"] = date("ymd-His");
// add $_REQUEST to infos
$infos["request"] = $_REQUEST;
// add $_FILES to infos
$infos["files"] = $_FILES;

// check if api call
$c = os::filter("var", "c", "public");
$m = os::filter("var", "m");

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

// add debug infos
$infos["debug"] = os::debug();

// return json data
header("Content-Type: application/json");
echo json_encode($infos, JSON_PRETTY_PRINT);
