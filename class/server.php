<?php

/**
 * class: server
 * creation: 2022-11-10 10:51:18
 * author: applh.com
 * license: MIT
 */


class server
{
    //@start_class

    static function run ()
    {
        // start PHP server
        // https://www.php.net/manual/en/features.commandline.webserver.php
        // example:
        // php gaia.php server::run

        // $cmd = "php -S localhost:8000";
        // $cmd = "php -S localhost:8000 -t /var/www/html/gaia";
        // $cmd = "php -S localhost:8000 -t /var/www/html/gaia -d display_errors=1";
        // $cmd = "php -S localhost:8000 -t /var/www/html/gaia -d display_errors=1 -d error_reporting=E_ALL";
        // $cmd = "php -S localhost:8000 -t /var/www/html/gaia -d display_errors=1 -d error_reporting=E_ALL -d error_log=/var/www/html/gaia/my-data/error.log";
        // $cmd = "php -S localhost:8000 -t /var/www/html/gaia -d display_errors=1 -d error_reporting=E_ALL -d error_log=/var/www/html/gaia/my-data/error.log -d log_errors=1";
        // $cmd = "php -S localhost:8000 -t /var/www/html/gaia -d display_errors=1 -d error_reporting=E_ALL -d error_log=/var/www/html/gaia/my-data/error.log -d log_errors=1 -d error_prepend_string='[gaia] '";
        // $cmd = "php -S localhost:8000 -t /var/www/html/gaia -d display_errors=1 -d error_reporting=E_ALL -d error_log=/var/www/html/gaia/my-data/error.log -d log_errors=1 -d error_prepend_string='[gaia] ' -d error_append_string='";

        $port = cli::parameters(2) ?? 8000;
        
        // get path root
        $path_root = gaia::kv("root");   
        $path_public = "$path_root/public";

        // change local path
        chdir($path_public);
        // PHP local server by router script    
        $cmd = "php -S localhost:$port $path_public/index.php";

        // launch shell command
        $output = shell_exec($cmd);
    }

    static function script ()
    {
        // load json data from parameter file
        $json = cli::param_json(2);
        // debug
        print_r($json);
        extract($json);

        $data ??= [];
        $sync_dirs ??= [];

        // upload all files in sync_dirs
        $sync_files = [];
        $upload_files = [];

        foreach ($sync_dirs as $sync_dir) {
            $path_data = gaia::kv("path_data");
            // get all files in sync_dir
            $files = glob("$path_data/$sync_dir/*");
            // debug
            print_r($files);
            // upload all files
            foreach ($files as $file) {
                // get file name
                $file_name = basename($file);
                // debug
                // echo "(file_name: $file_name)";
                $sync_files[] = $file_name;
                $upload_files[] = $file;

                // upload file
                // debug
            }
        }
        // $data["sync_files"] = $sync_files;

        // add upload files to Guzzle request
        $form_parts = [];
        foreach ($upload_files as $index => $file) {
            $form_parts[] = [
                "name" => "file$index",
                "contents" => GuzzleHttp\Psr7\Utils::tryFopen($file, "r"),
                "filename" => basename($file),
            ];
        }
        // add each data to Guzzle request
        foreach ($data as $key => $value) {
            $form_parts[] = [
                "name" => $key,
                "contents" => $value,
            ];
        }

        // send POST request with guzzle with uploads
        $client = new GuzzleHttp\Client();
        $response = $client->request("POST", $url, [
            "multipart" => $form_parts,
        ]);
        // get response body
        $body = $response->getBody();
        // get response body as string
        $body_string = $body->getContents();
        // debug
        echo $body_string;

    }

    //@end_class
}

//@end_file
