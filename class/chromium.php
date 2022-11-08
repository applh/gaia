<?php 

class chromium
{
    static function test ()
    {
        echo "(chromium test method)";
        extract(cli::param_json(2));
        $targetUrl = $url ?? "";
        $w ??= 1680;
        $h ??= 2160;

        // debug
        echo "(targetUrl: $targetUrl)";
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
            $page->navigate($targetUrl)->waitForNavigation();
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