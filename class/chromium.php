<?php 

class chromium
{
    static function web ()
    {
        echo "(chromium test method)";
        extract(cli::param_json(2));
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

    static function web_multiple ($urls)
    {
        extract(cli::param_json(2));
        $w ??= 1680;
        $h ??= 2160;
        $pdf_prefix ??= "";
        $chromium_exe ??= "chromium";
        $chromium_options ??= "";
        $slide_duration ??= 1;

        $now = date("ymd-His");
        // path root
        $path_data = gaia::kv("path_data");

        // create folder
        $path_frames = "$path_data/movies/frames-$now";
        mkdir($path_frames, 0777, true);

        foreach ($urls as $index => $url) {
            $md5 = md5($url);
            $index3 = str_pad($index, 3, "0", STR_PAD_LEFT);
            $targetFile = "$path_frames/f-$now-$index3.png";
            // $cmd = "chromium --headless --window-size=$w,$h --run-all-compositor-stages-before-draw --virtual-time-budget=10000 --screenshot=$targetFile $url";
            $cmd = "$chromium_exe --headless --disable-gpu --window-size=$w,$h --run-all-compositor-stages-before-draw $chromium_options --screenshot=$targetFile $url";
            echo "(cmd: $cmd)";
            $output = shell_exec($cmd);
            echo "(output: $output)";
        }

        // convert to pdf
        if ($pdf_prefix) {
            $target_pdf= "$path_frames/$pdf_prefix-$now.pdf";
            $cmd = "convert $path_frames/f-$now-*.png $target_pdf";
            echo "(cmd: $cmd)";
            $output = shell_exec($cmd);
            echo "(output: $output)";
        }

        // convert to movie
        if ($movie_prefix) {
            // build the concat file
            $concat_file = "$path_frames/concat-$now.txt";
            $concat = "";
            foreach ($urls as $index => $url) {
                $index3 = str_pad($index, 3, "0", STR_PAD_LEFT);
                $concat .= "file 'f-$now-$index3.png'\n";
                $concat .= "duration $slide_duration\n";
            }
            file_put_contents($concat_file, $concat);

            $target_movie= "$path_data/movies/$movie_prefix-$now.mp4";
            // $cmd = "ffmpeg -framerate 1/$slide_duration -i $path_data/screenshot-$now-%d.png -c:v libx264 -r 30 -pix_fmt yuv420p $target_movie";
            // $cmd = "ffmpeg -f concat -i $concat_file -c:v libx264 -pix_fmt yuv420p $target_movie";
            $cmd = "ffmpeg -f concat -i $concat_file -c:v libx264 -pix_fmt yuv420p $target_movie";
            echo "(cmd: $cmd)";
            $output = shell_exec($cmd);
            echo "(output: $output)";
        }
    }
}