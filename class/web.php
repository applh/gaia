<?php

class web
{
    // run the web application
    static function run ()
    {
        // get the request
        $request = request::get();

        // get the route
        $route = route::get($request);

        // get the controller
        $controller = controller::get($route);

        // get the response
        $response = response::get($controller);

        // send the response
        response::send($response);
    }
}