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
}