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
            $cache_disable = gaia::kv("cache_disable") ?? false;
            if (!$cache_disable) {
                $found = web::run_cache($path, $extension) ?? false;
            }
            else {
                $found = web::run_nocache($path, $extension) ?? false;
            }    
        }
    }

    static function run_cache ($path, $extension)
    {
        $cache_disable = gaia::kv("cache_disable") ?? false;

        if (!$cache_disable) {
            $found = web::cache_load($path, $extension) ?? false;
        }

        if (!$found) {
            ob_start();

            $found = web::load_template();
            $code = ob_get_clean();
            // save cache if needed
            if (!$cache_disable && gaia::kv("cache_save")) {
                web::cache_save($path, $code);
            }
            os::debug_headers();
            echo $code;
        }

    }

    static function run_nocache ()
    {
        // cache output to allow sending headers
        ob_start();
        $found = web::load_template();
        $code = ob_get_clean();
        os::debug_headers();
        echo $code;

        return $found;
    }

    static function cache_load($path, $extension)
    {
        $found = false;

        if (empty($_POST)) {

            $mime_type = web::mime($extension);
            os::cache($path);
            $cache_file = gaia::kv("os/cache/file") ?? "";
            if ($cache_file) {
                os::debug("cache_found($path,$extension,$mime_type($cache_file)");
                os::debug_headers();
                header("Content-Type: $mime_type");
                header("Cache-Control: public, max-age=31536000");
                $content = file_get_contents($cache_file);
                echo $content;
                $found = true;
            }
        }
        return $found;
    }

    static function cache_save($path, $content)
    {
        // save cache
        return os::cache($path, $content);
    }

    static function load_proxy($uri, $path)
    {
        $found = false;
        // if proxy is set then load content
        $proxy = gaia::kv("proxy");
        if ($proxy) {
            // $path = trim($path, "/");
            extract(pathinfo($path));

            $extension = $extension ?? "";

            if (!empty($_POST)) {
                $http_headers = [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => http_build_query($_POST),
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? "",
                ];
            } else {
                $http_headers = [
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? "",
                ];
            }
            $context = stream_context_create([
                "http" => $http_headers,
            ]);

            $url = "$proxy$uri";
            // $content = file_get_contents($url);
            $stream = fopen($url, 'r', false, $context ?? null);
            $meta_headers = stream_get_meta_data($stream);
            $content = stream_get_contents($stream);

            // get Content-Type from headers
            $headers = $meta_headers["wrapper_data"];
            foreach ($headers as $header) {
                if (strpos($header, "Content-Type") === 0) {
                    $mime_type = explode(":", $header)[1];
                    $mime_type = trim($mime_type);
                    break;
                }
            }

            $mime_type ??= web::mime($extension);
            header("Content-Type: $mime_type");
            os::debug("load_template($extension)($mime_type)($url)");

            echo $content;

            $found = true;
        }
        return $found;
    }

    static function load_template()
    {
        os::debug("load_template()");

        gaia::kv("cache_save", true);

        $uri = $_SERVER["REQUEST_URI"] ?? "";
        extract(parse_url($uri));
        $path = $path ?? "";

        if (web::load_proxy($uri, $path)) return true;

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
        // if extension is not php then set flag cache_save
        if ($extension == "php") {
            gaia::kv("cache_save", false);
        }

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
            $dir0 = form::filter("var", "", $dir0);

            $callback = "route_$dir0::check";
            os::debug("route $callback");
            if (is_callable($callback)) {
                $dir1 = form::filter("var", "", $dir1);
                $template = $callback($dir1, $filename, $extension);
            }
        }

        // check if template exists
        // if template exists then include it
        site::template($filename, $template);
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

    static function v($name, $default = "")
    {
        echo gaia::kv($name) ?? $default;
    }
}
