<?php 

class chromium
{
    static function test ()
    {
        echo "(chromium test method)";
        
        // my youtube video format
        // $w = 1680;
        // $h = 2160;

        // linkedin video format
        // $w = 720;
        // $h = 720;
        $w = 1680;
        $h = 2160;
        // https://github.com/chrome-php/chrome

        // replace default 'chrome' with 'chromium-browser' or 'chromium'
        $browserFactory = new HeadlessChromium\BrowserFactory($browserExe ?? "chromium");

        $browser = $browserFactory->createBrowser([
            'windowSize'   => [$w, $h],
        ]);
        try {
            $now = date("ymd-His");

            $targetUrl = "https://asimov.applh.com";
            $targetFile = __DIR__ . "/../my-data/screenshot-$now.png";

            $x = 0;
            $y = 0;
            $clip = new HeadlessChromium\Clip($x, $y, $w, $h);

            // load the page and take screenshot
            $page = $browser->createPage();
            $page->navigate($targetUrl)->waitForNavigation();
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