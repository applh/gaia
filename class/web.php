<?php

class web
{
    // run the web application
    static function run ()
    {
        // debug line
        echo date("Y-m-d H:i:s") . " (hello world)";

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