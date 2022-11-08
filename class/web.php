<?php

class web
{
    // run the web application
    static function run ()
    {
        // debug line

        $uri = $_SERVER["REQUEST_URI"] ?? "";
        $now = date("ymd-His");

        if ($uri == "/gitpull") {
            $infos = [];

            echo "(hello world: $uri)($now)";
            // execute git pull
            // `git pull`;
            $output = shell_exec("git pull");
            // return json data
            $infos["now"] = $now;
            $infos["uri"] = $uri;
            $infos["output"] = $output;

            // return json data
            header("Content-Type: application/json");
            echo json_encode($infos, JSON_PRETTY_PRINT);
        }
        else {
            // load template file templates/home.php
            require __DIR__ . "/../templates/home.php";
        }
        // // get the request
        // $request = request::get();

        // // get the route
        // $route = route::get($request);

        // // get the controller
        // $controller = controller::get($route);

        // // get the response
        // $response = response::get($controller);

        // // send the response
        // response::send($response);
    }
}