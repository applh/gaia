<?php

class web
{
    // run the web application
    static function run ()
    {
        // debug line

        $uri = $_SERVER["REQUEST_URI"] ?? "";
        $now = date("ymd-His");
        echo "(hello world: $uri)($now)";

        if ($uri == "/gitpull") {
            // execute git pull
            `git pull`;
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