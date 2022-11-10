<?php

class web
{
    // run the web application
    static function run ()
    {
        // router script
        if (php_sapi_name() == 'cli-server') {
            $res = web::check_asset();
            if ($res) {
                return;
            }
        }

        // debug line

        $uri = $_SERVER["REQUEST_URI"] ?? "";
        $now = date("ymd-His");

        if ($uri == "/gitpull") {
            $infos = [];

            // path root
            $path_root = gaia::kv("root");
            // change local path and execute git pull
            chdir($path_root);
            $cmd = "git pull";
            $output = shell_exec($cmd);

            // return json data
            $infos["now"] = $now;
            $infos["uri"] = $uri;
            $infos["output"] = $output;

            // return json data
            header("Content-Type: application/json");
            echo json_encode($infos, JSON_PRETTY_PRINT);
        }
        elseif ($uri == "/aframe") {
            // load template file templates/home.php
            require __DIR__ . "/../templates/aframe.php";
        }
        elseif ($uri == "/show") {
            // load template file templates/home.php
            require __DIR__ . "/../templates/revealjs.php";
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

    static function check_asset ()
    {
        // TODO: IMPROVE THIS FUNCTION
        $uri = $_SERVER["REQUEST_URI"] ?? "";
        extract(parse_url($uri));
        $path ??= "";

        if ($path) {
            // find the file in the public folder
            // and return it
            // root path
            $path_root = gaia::kv("root");
            // public path
            $path_public = "$path_root/public";
            // file path
            $path_file = "$path_public/$_SERVER[REQUEST_URI]";
            // file exists
            if (is_file($path_file)) {
                // get file extension
                $ext = pathinfo($path_file, PATHINFO_EXTENSION);
                // get mime type
                $mime = web::mime($ext);
                // set header
                header("Content-Type: $mime");
                // set header cache
                header("Cache-Control: public, max-age=31536000");
                // read file
                readfile($path_file);
                // exit
                return true;
            }
        }
        return false;
    }

    static function mime ($ext)
    {
        // return the mime type depending on the file extension
        static $mimes = [
            "css" => "text/css",
            "js" => "application/javascript",
            "mjs" => "application/javascript",
            "json" => "application/json",
            "webp" => "image/webp",
            "png" => "image/png",
            "jpg" => "image/jpeg",
            "jpeg" => "image/jpeg",
            "gif" => "image/gif",
            "svg" => "image/svg+xml",
            "ico" => "image/x-icon",
            "html" => "text/html",
            "txt" => "text/plain",
            "webm" => "video/webm",
            "mp4" => "video/mp4",
            "xml" => "text/xml",
            "pdf" => "application/pdf",
            "zip" => "application/zip",
            "doc" => "application/msword",
            "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "xls" => "application/vnd.ms-excel",
            "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "ppt" => "application/vnd.ms-powerpoint",
            "pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
            "mp3" => "audio/mpeg",
            "wav" => "audio/wav",
            "avi" => "video/x-msvideo",
            "php" => "text/plain",
        ];

        $mime = $mimes[$ext] ?? "application/octet-stream";

        return $mime;
    }

    static function slides ($mdfile)
    {
        $blocs = os::markdown($mdfile);
        // build section with each bloc content
        $sections = [];
        foreach ($blocs as $bloc) {
            $title = $bloc["title"] ?? "";
            $content = $bloc["content"] ?? "";

            $sections[] = 
            <<<html
            <section data-markdown>
                <textarea data-template>
                $title

                $content
                </textarea>
            </section>
            html;
        }
        // join sections
        $sections = implode("", $sections);

        echo $sections;

        // return sections
        return $sections;

    }
}