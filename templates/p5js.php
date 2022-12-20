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
    </style>
    <script src="/media/p5js/p5.min.js"></script>
    <script>
        function setup() {
            // windowWidth, windowHeight
            let ww = window.innerWidth;
            let wh = window.innerHeight;

            createCanvas(ww, wh, WEBGL);
            // https://show.microwebagency.com/media/images/code.jpg
            let src1 = "/media/images/code.jpg";
            let src2 = "/media/images/earth-night.jpg";
            img1 = loadImage(src1); // Load the image
            img2 = loadImage(src2); // Load the image

            // sliders
            rSlider = createSlider(0, 1000, 0, 1);
        }

        function windowResized() {
            resizeCanvas(innerWidth, innerHeight);
        }

        // let plane_z = 0;

        function draw() {
            let w = width * 0.5;
            let h = height * 0.5;

            rSlider.position(0, height -20);
            rSlider.style('width', width + 'px');

            let plane_z = rSlider.value();

            background(0);
            let locX = mouseX - height / 2;
            let locY = mouseY - width / 2;

            ambientLight(255, 255, 255);
            pointLight(255, 255, 255, 0, 0, 200);
            // pointLight(255, 255, 255, locX, locY, 100);
            
            // Displays the image at its actual size at point (0,0)
            // image(img, 0, 0);
            // resize image at half canvas dimensions 
            // and display image at center of canvas
            let x = width * 0.25;
            let y = height * 0.25;
            // image(img1, x, y, w, h);

            // image(img, width * 0.25, height * 0.25, img.width * 0.5, img.height * 0.5);

            // image(img, 0, height * 0.25, width * 0.5, height * 0.5 * width / img.width);

            // CUBE
            noStroke();
            fill(50);
            push();
            translate(-w +200, h-200);
            rotateY(1.25);
            rotateX(-0.9);
            // specularMaterial(250);
            texture(img1);
            box(100);
            pop();

            // EARTH
            // noFill();
            // stroke(255);
            push();
            translate(w -250, h -250, 200);
            rotateY(0.01 * plane_z);
            texture(img2);
            sphere(100);
            pop();

            // SLIDE
            noStroke();
            // fill(250);
            push();
            // translate(0, 0, plane_z);
            // scale(1 + 0.1 * plane_z);
            scale(max(10 - 0.01 * plane_z, 1));
            // plane_z += 1;

            texture(img1);
            plane(img1.width, img1.height);
            pop();

        }
    </script>
</head>

<body>
    <main>
    </main>
</body>

</html>