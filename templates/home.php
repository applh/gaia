<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GAIA">
    <title>GAIA</title>
    <link rel="stylesheet" href="/media/site.css">
    <style>
    </style>
    <script type="module" src="/media/site.mjs"></script>
</head>
<body>
    <header>

    </header>
    <main>
        <h1><a href="/">GAIA</a></h1>
        <section class="s2">
            <h2>title 2A</h2>
            <p>content2</p>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
        </section>
        <section class="s2">
            <h2>title 2B</h2>
            <p>content2</p>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
        </section>
        <section class="s2">
            <h2>title 2C</h2>
            <p>content2</p>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
        </section>
        <section class="s2">
            <h2>title 2C</h2>
            <p>content2</p>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
        </section>
        <section class="s2">
            <h2>title 2C</h2>
            <p>content2</p>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
        </section>
        <section class="s2">
            <h2>title 2C</h2>
            <p>content2</p>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
            <section class="s3">
                <h3>title3</h3>
                <p>content3</p>
            </section>
        </section>
    </main>
    <footer>

    </footer>
    <!-- vuejs -->
    <!-- add vuejs app from CDN -->
    <div id="app"></div>
    <template id="appVue">
        <h3>{{ message }}</h3>
    </template>
    <script type="module">
        // add vuejs app from CDN
        import { createApp, defineComponent } from "https://cdn.jsdelivr.net/npm/vue@3/dist/vue.esm-browser.js";
        const App = defineComponent({
            template: "#appVue",
            data() {
                return {
                    message: "Hello Vue 3!"
                }
            }
        });
        createApp(App).mount("#app");

    </script>
</body>
</html>