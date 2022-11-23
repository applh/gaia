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

        $url ??= "";
        $zip_list ??= [];
        $zip_list_name ??= "";

        $client = new GuzzleHttp\Client();

        if (!empty($zip_list) && $zip_list_name) {
            // add upload files to Guzzle request
            $form_parts = [];
            // add each data to Guzzle request
            foreach ($zip_list as $key => $value) {
                $form_parts[] = [
                    "name" => $key,
                    "contents" => $value,
                ];
            }
            try {
                $response = $client->request("POST", $url, [
                    "multipart" => $form_parts,
                ]);
                // debug
                $status = $response->getStatusCode();
                $body = $response->getBody();
                $content = $body->getContents();
                $list_response = json_decode($content, true);
                $list_files = $list_response["$zip_list_name"];
                // debug
                print_r($list_files);
            } catch (GuzzleHttp\Exception\GuzzleException $e) {
                // request can timeout due to server limitations
                echo $e->getMessage(); 
            }
        }

        $data ??= [];
        $sync_dirs ??= [];
        $done_dirs ??= [];

        // upload all files in sync_dirs
        $sync_files = [];
        $upload_files = [];
        $done_files = [];

        foreach ($sync_dirs as $index_dir => $sync_dir) {
            $path_data = gaia::kv("path_data");
            // get all files in sync_dir
            $files = glob("$path_data/$sync_dir/*");
            $done_dir = $done_dirs[$index_dir] ?? "";
            // create done_dir if not exists
            if (!is_dir("$path_data/$done_dir")) {
                mkdir("$path_data/$done_dir", 0777, true);
            }

            // debug
            print_r($files);
            // upload all files
            foreach ($files as $index_file => $file) {
                // get file name
                $file_name = basename($file);
                // debug
                // echo "(file_name: $file_name)";
                // check if file is already in zip_list
                if (!in_array($file_name, $list_files)) {
                    $sync_files[] = $file_name;
                    $upload_files[] = $file;
                    if ($done_dir) {
                        $done_files[] = "$path_data/$done_dir/$file_name";
                    }
                    else {
                        $done_files[] = "";
                    }
                }
                else {
                    // debug
                    echo "($index_file)(already in zip: $file_name)\n";
                    // move file to done_dir
                    if ($done_dir) {
                        rename($file, "$path_data/$done_dir/$file_name");
                    }
                }
            }
        }
        // $data["sync_files"] = $sync_files;

        // add upload files to Guzzle request
        $form_parts = [];
        // add each data to Guzzle request
        foreach ($data as $key => $value) {
            $form_parts[] = [
                "name" => $key,
                "contents" => $value,
            ];
        }

        // O2Switch is blocking too many files in one request (>40Mo?) 
        // send POST request with guzzle with uploads
        $status = 0;
        $errors = [];
        $size_total = 0;
        foreach ($upload_files as $index => $file) {
            $size = filesize($file);

            // copy the basics infos from the the parts
            $upload_parts = $form_parts;
            // add one file to the parts
            $upload_parts[] = [
                "name" => "file$index",
                "contents" => GuzzleHttp\Psr7\Utils::tryFopen($file, "r"),
                "filename" => basename($file),
            ];
            try {
                $response = $client->request("POST", $url, [
                    "multipart" => $upload_parts,
                ]);
                // debug
                $status = $response->getStatusCode();

                // get response body
                $body = $response->getBody();
                // get response body as string
                $body_string = $body->getContents();
            } catch (GuzzleHttp\Exception\GuzzleException $e) {
                // request can timeout due to server limitations
                // debug
                $body_string = $e->getMessage();
                $errors["$file"] = "($status)$size";
                // debug total size
                echo "($index)(size_total: $size_total)";
            }

            if ($status != 200) {
                $errors["$file"] = "($status)$size";
            }
            else {
                // get file size
                $size_total += $size;
                // move file to done dir if not empty
                $done_file = $done_files[$index];
                if ($done_file) {
                    // move file
                    rename($file, $done_file);
                }
            }
            // debug
            echo <<<txt
            ---
            ($index)($size/$size_total)
            $file
            ---
            $body_string
            ---

            txt;
        }
        print_r($errors);
    }

    static function upload_files ()
    {
        $url = gaia::kv("server/send/url") ?? "";
        $search = gaia::kv("server/send/dir") ?? "";
        $data_inputs = gaia::kv("server/send/data_inputs") ?? [];
        $files = [];
        if ($search) {
            $path_data = gaia::kv("path_data");
            $search2 = "$path_data/$search";
            $files = glob($search2);
            
            print_r($files);    
        }

        if (!empty($files) && $url) {
            // upload file by curl
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);

            $nb_files = count($files);
            foreach($files as $index => $file) {
                $name = basename($file);
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                        
                // add data
                $data = $data_inputs;
                $data["file-$index"] = new CURLFile($file, 'image/' . $ext, $name);
                print_r($data);
                echo "(url($index/$nb_files):$url)\n";
            
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);            
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $res = curl_exec($ch);
                // get status code
                $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                // get error
                $error = curl_error($ch);
                echo "$status|$error|$res";
            
            }
            // close curl
            curl_close($ch);
     
        }
       
    }

    //@end_class
}

//@end_file
