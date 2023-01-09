<?php
// https://www.php.net/manual/en/features.commandline.webserver.php
// php -S localhost:9876

$now = date('Y-m-d H:i:s');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TITLE</title>

    <link rel="stylesheet" href="/media/uikit/uikit.min.css">
    <link rel="stylesheet" href="/media/site.css">
    <link rel="stylesheet" href="/media/leaflet/leaflet.css" crossorigin="">

</head>

<body>
    <h1>TITLE1</h1>
    <h2><?php echo $now ?></h2>
    <img src="/media/images/code.jpg" alt="">

    <section data-uk-scrollspy="cls:uk-animation-fade">
        <h2>uikit</h2>
        <div data-uk-sortable>
            <div>ITEM 1</div>
            <div>ITEM 2</div>
            <div>ITEM 3</div>
            <div>ITEM 4</div>
        </div>
    </section>
    <section data-uk-scrollspy="cls:uk-animation-slide-top">
        <h2>uikit</h2>
        <div data-uk-sortable>
            <div>ITEM 1</div>
            <div>ITEM 2</div>
            <div>ITEM 3</div>
            <div>ITEM 4</div>
        </div>
    </section>
    <section data-uk-scrollspy="cls:uk-animation-scale-up">
        <h2>uikit</h2>
        <div class="uk-height-large uk-background-cover uk-overflow-hidden uk-dark uk-flex" style="background-image: url('/media/images/code.jpg');">
            <div class="uk-width-1-2@m uk-text-center uk-margin-auto uk-margin-auto-vertical">
                <h1 uk-parallax="opacity: 0,1; y: -100,0; scale: 2,1; end: 50vh + 50%;">Headline</h1>
                <p uk-parallax="opacity: 0,1; y: 100,0; scale: 0.5,1; end: 50vh + 50%;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            </div>
        </div>
    </section>

    <section>
        <div id="map" style="width: 100%; height: 80vmin"></div>
    </section>

    <footer>

    </footer>
    <section id="app">
    </section>
    <template id="appTemplate">
        <h3>{{ message }}</h3>
        <p>vue is ready</p>
        <div data-uk-sortable>
            <div>ITEM 1</div>
            <div>ITEM 2</div>
            <div>ITEM 3</div>
            <div>ITEM 4</div>
        </div>
    </template>
    <script src="/media/uikit/uikit.min.js"></script>
    <script src="/media/uikit/uikit-icons.min.js"></script>
    <script src="/media/leaflet/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
    <script type="module" src="/media/site.js"></script>
</body>

</html>