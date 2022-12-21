<html>

<head>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        .hid {
            display: none;
        }
    </style>
    <script src="/media/p5js/p5.min.js"></script>
    <script>
        let img1 = null;
        let slide_url = null;
        let slide_text_dyn = null;
        let slide_title_dyn = null;
        let slide_title_size = 60;
        let frame_scale = 10;

        function set_frame_scale (scale) {
            frame_scale = scale;
        }

        async function set_slide_img (url, wait=500) {
            if (slide_url == null) {
                img1 = loadImage(url, img => {
                    slide_url = url;
                    // console.log(img);
                    // console.log(img.width);
                    // console.log(img.height);
                });
                // wait image load
                await new Promise(r => setTimeout(r, wait));
            }
            else if (url != slide_url) {
                slide_url = url;
                img1 = loadImage(url);
                // wait image load
                await new Promise(r => setTimeout(r, wait));
            }
        }

        function add_slide_line (text) {
            slide_text_dyn ??= '';
            slide_text_dyn += '\n' + text;
        }

        function set_slide_title (title, size=60) {
            slide_title_dyn = title;
            slide_title_size = size;
        }

        function set_slide_text (text) {
            slide_text_dyn = text;
        }

        function preload() {
            // fonts
            font1 = loadFont('/media/p5js/RobotoMono-Regular.ttf');

            // images
            let src1 = "/media/images/code.jpg";
            let src2 = "/media/images/earth-night.jpg";
            img1 = loadImage(src1); // Load the image
            img2 = loadImage(src2); // Load the image
        }

        function setup() {
            // windowWidth, windowHeight
            let ww = window.innerWidth;
            let wh = window.innerHeight;

            createCanvas(ww, wh, WEBGL);

            let w50 = 0.5 * width;
            let h50 = 0.5 * height;
            // ortho(-w50, w50, -h50, h50, -1000, 1000);

            // fonts
            textFont(font1);

            // https://show.microwebagency.com/media/images/code.jpg

            // sliders
            rSlider = createSlider(0, 1000, 0, 1);

        }

        function windowResized() {
            resizeCanvas(innerWidth, innerHeight);
        }

        // let plane_z = 0;

        function draw() {

            // get the url hash
            let hash = window.location.hash;
            if (hash) {
                // console.log(hash);
                // remove the # from the hash
                hash = hash.substring(2);
                // convert the hash to an integer
                hash = parseInt(hash);
                // set the slider value to the hash
                rSlider.value(frame_scale * hash);
            }

            let w = width * 0.5;
            let h = height * 0.5;

            rSlider.position(20, height-20);
            rSlider.style('width', (width-40) + 'px');

            let plane_z = rSlider.value();

            let locX = mouseX - height / 2;
            let locY = mouseY - width / 2;

            // LIGHTS
            background(0);
            ambientLight(255, 255, 255);
            // lightFalloff(1, 0, 0);
            let light_z = plane_z * 0.100;
            pointLight(100 + light_z, light_z, light_z, 0, 0, 300);
            // lightFalloff(1, 0, 0);
            pointLight(255, 255, 255, 0, 0, 600);
            pointLight(255 - 2 * light_z, 0, 0, w -100, h -100, 300);
            // pointLight(255, 255, 255, locX, locY, 100);
            
            // Displays the image at its actual size at point (0,0)
            // image(img, 0, 0);
            // resize image at half canvas dimensions 
            // and display image at center of canvas
            let x = width * 0.25;
            let y = height * 0.25;

            // CUBE
            // noStroke();
            // fill(50);
            // push();
            // translate(-w +100, h-100, 100);
            // rotateX(-0.9);
            // rotateY(0.01 * plane_z);
            // // specularMaterial(250);
            // texture(img1);
            // box(100);
            // pop();


            // SLIDE
            noStroke();
            // fill(250);
            push();
            let scale_min = height / img1.height;
            let scale_slide = max(8 - 0.01 * plane_z, scale_min);
            let slide_dx = max(0, 0.25 * (width - img1.width * scale_slide));
            scale(scale_slide);
            translate(slide_dx, 0, 0);
            // plane_z += 1;

            texture(img1);
            shininess(100);
            plane(img1.width, img1.height);
            pop();

            // EARTH
            // noFill();
            // stroke(255);
            // lightFalloff(0.97, 0.03, 0);
            push();
            // translate(w -150, h -150, 100);
            translate(00, h -150, 100);
            rotateY(TWO_PI * 0.001 * plane_z -PI);
            texture(img2);
            scale(0.5);
            sphere(100);
            pop();


            // title
            if (slide_title_dyn) {
                push();
                textSize(slide_title_size);
                translate(-w +80, -h +80, 10);
                fill(255, 255, 255);
                textAlign(LEFT, TOP);

                text(slide_title_dyn, 0, 0);
                pop();
            }

            // text
            let slide_text = slide_text_dyn ?? document.querySelector("#slide_text").innerHTML;
            let lmax = slide_text.length * 0.005 * plane_z;
            slide_text = slide_text.substring(0, lmax);
            push();
            textSize(40);
            translate(-w +80, -h + (3 * slide_title_size), 10);
            fill(255, 255, 255);
            textAlign(LEFT, TOP);

            text(slide_text, 0, 0);
            pop();

        }
    </script>
    <pre class="hid" id="slide_text">
* Hello Gaia.
* 2nd Line.
* Third Line.
* My Line 5.
* My Line 6.
* My Line 7.
* My Line 8.
* My Line 9.
* My Line 10.

    </pre>
</head>

<body>
    <main>
    </main>
</body>

</html>