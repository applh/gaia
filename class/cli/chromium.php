<?php

use chromium as GlobalChromium;

class chromium
{
    static function web()
    {
        echo "(chromium test method)";
        extract(cli::param_json(2) ?? []);
        $targetUrl = $url ?? "";
        $w ??= 1680;
        $h ??= 2160;
        $timeout ??= 60000;
        $sleep ??= 5;
        $retry_max ??= 0;

        // load multiple urls
        $urls ??= [];
        if (count($urls) > 0) {
            chromium::web_multiple($urls);
            return;
        }

        // debug
        echo "(targetUrl: $targetUrl)(timeout: $timeout)";
        // if targetUrl is not set then exit
        if (!$targetUrl) {
            echo "(targetUrl is not set)";
            return;
        }

        // my youtube video format
        // $w = 1680;
        // $h = 2160;

        // linkedin video format
        // $w = 720;
        // $h = 720;
        // https://github.com/chrome-php/chrome

        // replace default 'chrome' with 'chromium-browser' or 'chromium'
        $browserFactory = new HeadlessChromium\BrowserFactory($browserExe ?? "chromium");

        $browser = $browserFactory->createBrowser([
            'windowSize'   => [$w, $h],
        ]);
        try {
            $now = date("ymd-His");

            $targetFile = __DIR__ . "/../my-data/screenshot-$now.png";

            $x = 0;
            $y = 0;
            $clip = new HeadlessChromium\Clip($x, $y, $w, $h);

            // load the page and take screenshot
            $page = $browser->createPage();
            // $page->navigate($targetUrl)->waitForNavigation(HeadlessChromium\Page::NETWORK_IDLE, $timeout);
            $page->navigate($targetUrl)->waitForNavigation(HeadlessChromium\Page::LOAD, $timeout);

            $retry = 0;
            while ($retry < $retry_max) {
                // evaluate script in the browser
                $evaluation = $page->evaluate('document.querySelectorAll(".a-enter-vr-button").length;') ?? 0;

                // wait for the value to return and get it
                $value = $evaluation->getReturnValue($timeout);
                echo "(value-$retry: $value)";
                if ($value  > 0) {
                    break;
                }

                sleep($sleep);
                $retry++;
            }
            // evaluate script in the browser
            $evaluation = $page->evaluate('document.querySelectorAll(".a-enter-vr-button").forEach((e) => e.style.display = "none");') ?? "";

            // wait for the value to return and get it
            $value = $evaluation->getReturnValue($timeout);
            echo "(value: $value)";

            $html = $page->getHtml();
            // debug
            echo "$html";

            $page
                ->screenshot([
                    'captureBeyondViewport' => true,
                    'clip'  => $clip,
                ])
                ->saveToFile($targetFile);
        } finally {
            $browser->close();
        }
    }

    static function web_multiple($urls)
    {
        extract(cli::param_json(2));
        $w ??= 1680;
        $h ??= 2160;
        $pdf_prefix ??= "";
        $chromium_exe ??= "chromium";
        $chromium_options ??= "";
        $slide_duration ??= 1;
        $urls ??= [];
        $path_data ??= gaia::kv("path_data");
        $path_publish ??= "";

        $urls_max ??= count($urls);
        $urls_min ??= 0;

        $cmdjs ??= [];

        $now = date("ymd-His");
        // path root

        // create folder
        $path_frames = "$path_data/movies/frames-$now";
        mkdir($path_frames, 0777, true);
        chmod($path_frames, 0777);

        $concat = "";
        for ($index = $urls_min; $index < $urls_max; $index++) {
            $url = trim($urls[$index] ?? "");
            if ($url) {
                $index3 = str_pad($index, 3, "0", STR_PAD_LEFT);
                $targetFile = "$path_frames/f-$now-$index3.png";
                // FIXME: on linux, can't create file in /var/www/ ???
                // touch($targetFile);
                // chmod($targetFile, 0666);

                // $cmd = "chromium --headless --window-size=$w,$h --run-all-compositor-stages-before-draw --virtual-time-budget=10000 --screenshot=$targetFile $url";
                // $cmd = "$chromium_exe --headless --disable-gpu --window-size=$w,$h --run-all-compositor-stages-before-draw $chromium_options --screenshot=$targetFile $url";
                // $output = shell_exec($cmd);
                $close = true;
                chromium::screenshot($url, $targetFile, $w, $h, $timeout, $cmdjs, $close);

                $concat .= "file 'f-$now-$index3.png'\n";
                $concat .= "duration $slide_duration\n";

                echo
                <<<txt
                ---
                (index: $index)
                (url: $url)
                (targetFile: $targetFile)
                ---
    
                txt;
            }
        }

        // convert to pdf
        if ($pdf_prefix) {
            $target_pdf = "$path_frames/$pdf_prefix-$now.pdf";
            $cmd = "convert $path_frames/f-$now-*.png $target_pdf";
            echo "(cmd: $cmd)";
            $output = shell_exec($cmd);
            echo "(output: $output)";

            // copy to path_publish if set
            if ($path_publish) {
                $target_pdf_publish = "$path_publish/$pdf_prefix-$now.pdf";
                copy($target_pdf, $target_pdf_publish);
            }
        }

        // convert to movie
        if ($movie_prefix) {
            // build the concat file
            $concat_file = "$path_frames/concat-$now.txt";
            file_put_contents($concat_file, $concat);

            $target_movie = "$path_data/movies/$movie_prefix-$now.mp4";
            // $cmd = "ffmpeg -framerate 1/$slide_duration -i $path_data/screenshot-$now-%d.png -c:v libx264 -r 30 -pix_fmt yuv420p $target_movie";
            // $cmd = "ffmpeg -f concat -i $concat_file -c:v libx264 -pix_fmt yuv420p $target_movie";
            $cmd = "ffmpeg -f concat -i $concat_file -c:v libx264 -pix_fmt yuv420p $target_movie";
            echo "(cmd: $cmd)";
            $output = shell_exec($cmd);
            echo "(output: $output)";
            echo "($target_movie)";

            // copy to path_publish if set
            if ($path_publish) {
                $target_movie_publish = "$path_publish/$movie_prefix-$now.mp4";
                copy($target_movie, $target_movie_publish);

                echo "($target_movie_publish)";
            }
        }
    }

    /**
     * WARNING: 
     * trying to re-use the same browser for all screenshots is NOT working ?!
     * => timeout on the second screenshot ?!
     */
    static function screenshot($targetUrl = "", $targetFile = "", $w = 1200, $h = 1200, $timeout = 10000, $cmdjs = [], $close = true)
    {
        $browser = null;
        $page = null;
        $clip = null;

        if ($browser == null) {
            // replace default 'chrome' with 'chromium-browser' or 'chromium'
            $browserFactory = new HeadlessChromium\BrowserFactory($browserExe ?? "chromium");

            $browser = $browserFactory->createBrowser([
                'windowSize'   => [$w, $h],
            ]);
            $page = $browser->createPage();
            $clip = new HeadlessChromium\Clip(0, 0, $w, $h);
        }

        try {
            // load the page and take screenshot

            // $page->navigate($targetUrl)->waitForNavigation(HeadlessChromium\Page::NETWORK_IDLE, $timeout);
            $page->navigate($targetUrl)->waitForNavigation(HeadlessChromium\Page::LOAD, $timeout);

            foreach ($cmdjs as $cmd) {
                echo "($cmd)";

                $ev = $page->evaluate($cmd);
                $res = $ev->getReturnValue($timeout) ?? "";

                echo "($res)\n";
            }

            $page
                ->screenshot([
                    'captureBeyondViewport' => true,
                    'clip'  => $clip,
                ])
                ->saveToFile($targetFile, $timeout);
        } finally {
            if ($close) {
                $browser->close();
                $browser = null;
            }
        }
    }


    static function read()
    {
        extract(cli::param_json(2));

        $w ??= 1680;
        $h ??= 2160;
        $timeout ??= 10000;

        $targetUrl ??= "";
        $codeJs ??= [];
        $saveImages ??= false;

        if (!$targetUrl) {
            echo "targetUrl is required";
            return;
        }

        // open a chromium browser
        $browserFactory = new HeadlessChromium\BrowserFactory($browserExe ?? "chromium");

        $browser = $browserFactory->createBrowser([
            'windowSize'   => [$w, $h],
        ]);
        try {
            // load the page and take screenshot
            $page = $browser->createPage();
            // $page->navigate($targetUrl)->waitForNavigation(HeadlessChromium\Page::NETWORK_IDLE, $timeout);
            $page->navigate($targetUrl)->waitForNavigation(HeadlessChromium\Page::LOAD, $timeout);

            $html = $page->getHtml();
            // debug
            // echo "$html";

            $evaluations = [];
            $results = [];

            foreach ($codeJs as $code) {
                // debug
                echo "(code: $code)\n";
                // evaluate script in the browser
                $evaluation = $page->evaluate($code) ?? "";
                $evaluations[] = $evaluation;
                $results[] = $evaluation->getReturnValue($timeout);
            }

            $res = implode("\n", $results);

            $res = strip_tags($res);
            $res = str_replace("*", "\n\n", $res);
            echo $res;

            // save to file my-data/movies/news-%date%/content.txt
            $path_data = gaia::kv("path_data");
            $now = date("ymd-His");
            $path_news = "$path_data/movies/news";
            if (!is_dir($path_news)) {
                mkdir($path_news, 0777, true);
            }
            $targetFile = "$path_news/content-$now.txt";
            file_put_contents($targetFile, $res);

            if ($saveImages) {
                // find all images urls ending with jpg, jpeg, png, webp in $res
                $matches = [];
                preg_match_all("/(https?:\/\/.*\.(?:jpg|jpeg|png|webp))/i", $res, $matches);
                $images = $matches[1];
                // debug
                print_r($images);
                // save images
                $index = 0;
                foreach ($images as $image) {
                    $index++;
                    $image = imagecreatefromstring(file_get_contents($image));
                    // save the image in webp format in folder my-data/movies/webp
                    $path_webp = "$path_data/movies/webp";
                    // create folder if not exists
                    if (!file_exists($path_webp)) {
                        mkdir($path_webp, 0777, true);
                    }
                    $targetFile = "$path_news/image-$index.webp";
                    // debug
                    echo "($targetFile)\n";

                    imagewebp($image, $targetFile);
                    // md5 the image
                    $md5 = md5_file($targetFile);
                    // rename the image
                    $targetWebp = "$path_webp/image-$md5.webp";
                    // create the file if not exists
                    if (!file_exists($targetWebp)) {
                        rename($targetFile, $targetWebp);
                    } else {
                        // delete the file
                        unlink($targetFile);
                    }
                }
            }
        } finally {
            $browser->close();
        }
    }
}
