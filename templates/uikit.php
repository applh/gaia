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
    <script type="module">
console.log('site.js loaded');

// import vuesJS from CDN and create Vue app
import * as Vue from 'https://cdn.jsdelivr.net/npm/vue@3.2.45/dist/vue.esm-browser.prod.js';
const app = Vue.createApp({
    template: '#appTemplate',
    mounted () {
        const map = L.map('map').setView([51.505, -0.09], 13);

        const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
    
        const marker = L.marker([51.5, -0.09]).addTo(map)
            .bindPopup('<b>Hello world!</b><br />I am a popup.').openPopup();
    
        const circle = L.circle([51.508, -0.11], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: 500
        }).addTo(map).bindPopup('I am a circle.');
    
        const polygon = L.polygon([
            [51.509, -0.08],
            [51.503, -0.06],
            [51.51, -0.047]
        ]).addTo(map).bindPopup('I am a polygon.');
    
    
        const popup = L.popup()
            .setLatLng([51.513, -0.09])
            .setContent('I am a standalone popup.')
            .openOn(map);
    
        function onMapClick(e) {
            popup
                .setLatLng(e.latlng)
                .setContent(`You clicked the map at ${e.latlng.toString()}`)
                .openOn(map);
        }
    
        map.on('click', onMapClick);
    },
    data() {
        return {
            message: 'Hello Vue!'
        }
    }
});
// mount Vue app
app.mount('#app');



    </script>
</body>

</html>