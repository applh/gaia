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
        extract(parse_url($uri));
        $path = $path ?? "";
        // special case for root
        if ($path == "/") {
            $path = "/index.php";
        }
        $now = date("ymd-His");

        // path root
        $path_root = gaia::kv("root");

        $path = trim($path, "/");
        extract(pathinfo($path));

        $dirname = $dirname ?? "";
        $filename = $filename ?? "";
        $extension = $extension ?? "";

        // routes
        $routes = gaia::kv("routes") ?? [
            "index" => "home.php",
            "api" => "api.php",
            "aframe" => "aframe.php",
            "show" => "revealjs.php",
            "robots" => "robots.php",
            "gitpull" => "gitpull.php",            
        ];

        // check if route exists
        $template = $routes[$filename] ?? "404.php";
        // check if template exists
        $templateFile = "$path_root/templates/$template";
        // if template exists then include it
        if (file_exists($templateFile)) {
            include($templateFile);
        } else {
            // if template does not exist then return 404
            // header("HTTP/1.0 404 Not Found");
            // echo "404 Not Found";
        }        
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