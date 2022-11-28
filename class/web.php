<?php

class web
{
    // run the web application
    static function run()
    {

        $uri = $_SERVER["REQUEST_URI"] ?? "";
        extract(parse_url($uri));
        $path = $path ?? "";
        // special case for root
        if ($path == "/") {
            $path = "/index.html";
        }
        extract(pathinfo($path));
        $extension ??= "";

        // load domain config
        site::setup();

        // router script
        $found = false;
        if (php_sapi_name() == 'cli-server') {
            $found = web::check_asset();
        }

        if (!$found) {
            $found = web::cache_load($path, $extension) ?? false;
        }
        
        if (!$found) {
            ob_start();

            $found = web::load_template();
            $code = ob_get_clean();
            // save cache if needed
            if (gaia::kv("cache_save")) {
                web::cache_save($path, $code);
            }
            os::debug_headers();
            echo $code;
        }


    }

    static function cache_load ($path, $extension)
    {
        $found = false;

        if (empty($_POST)) {
            $mime_type = web::mime($extension);
            os::debug("cache_load($path, $extension) $mime_type");
            header("Content-Type: $mime_type");
            $found = os::cache($path, show:true);    
        }
        return $found;
    }

    static function cache_save ($path, $content)
    {
        // save cache
        return os::cache($path, $content);
    }

    static function load_template ()
    {
        os::debug("load_template()");

        $uri = $_SERVER["REQUEST_URI"] ?? "";
        extract(parse_url($uri));
        $path = $path ?? "";
        // special case for root
        if ($path == "/") {
            $path = "/index.html";
        }
        $now = date("ymd-His");

        // path root
        $path_root = gaia::kv("root");

        // $path = trim($path, "/");
        extract(pathinfo($path));

        $dirname = trim($dirname ?? "", "/");

        $filename = $filename ?? "";
        $extension = $extension ?? "";

        if ($dirname) {
            $parts = explode("/", $dirname);
        } else {
            $parts = [];
        }

        $nb_parts = count($parts);

        $template = "";
        // print_r($parts);
        // var_dump($dirname);

        if ($nb_parts == 0) {
            // routes
            $routes = gaia::kv("routes") ?? [
                "index" => "home.php",
                "api" => "api.php",
                "robots" => "robots.php",
                "gitpull" => "gitpull.php",
            ];

            // check if route exists
            $template = $routes[$filename] ?? "404.php";
        } else {
            // dynamic routes
            $dir0 = $parts[0] ?? "";
            $dir1 = $parts[1] ?? $filename; //FIXME

            // TODO: better filter ?
            $dir0 = os::filter("var", "", $dir0);

            $callback = "route_$dir0::check";
            os::debug("route $callback");
            if (is_callable($callback)) {
                $dir1 = os::filter("var", "", $dir1);
                $template = $callback($dir1, $filename, $extension);
            }
        }

        // check if template exists
        // if template exists then include it
        site::template($filename, $template);

        // if extension is not php then set flag cache_save
        if (empty($_POST) && ($extension != "php")) {
            gaia::kv("cache_save", true);
        }
    }


    static function check_asset()
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

    static function mime($ext)
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
            "php" => "text/html",
        ];

        $mime = $mimes[$ext] ?? "application/octet-stream";

        return $mime;
    }

    static function slides()
    {
        $max_slide = gaia::kv("web/max_slide") ?? 100;
        $mdfile = gaia::kv("web/slides") ?? "pages/project-blog.md";

        $blocs = os::markdown($mdfile);
        // build section with each bloc content
        $sections = [];
        $count = 0;
        foreach ($blocs as $index => $bloc) {
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

            $count++;
            if ($count >= $max_slide)
                break;
        }
        // join sections
        $sections = implode("", $sections);

        echo $sections;

        // return sections
        return $sections;
    }

    static function v ($name, $default = "")
    {
        echo gaia::kv($name) ?? $default;
    }
}
