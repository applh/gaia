<?php

/**
 * class: movie
 * creation: 2022-11-09 10:22:59
 * author: applh.com
 * license: MIT
 */


class movie
{
    //@start_class

    static function build_frames ()
    {
        // load config from json parameter file
        extract(cli::param_json(2));

        $title ??= "";
        $slides ??= [];
        $markdown ??= "";
        $font_size ??= 40;

        if ($markdown) {
            // get all titles
            $blocs = gaia::kv("os/markdown/blocs") ?? [];
            foreach($blocs as $line => $bloc) {
                $slide_title = $bloc["title"] ?? "";
                $slide_content = $bloc["content"] ?? "";

                // $slide_title = str_replace("#", "", $slide_title);
                $slide_title = trim($slide_title);
                $slides[] = [ 
                    "title" => $slide_title,
                    "content" => $slide_content,
                ];
            }
            // debug
            print_r($slides);
        }

        // get data path
        $path_data = gaia::kv("path_data");
        // movies path
        $now = date("ymd-His");
        $path_movies = "$path_data/movies/frames-$now";
        // create the movies path if it does not exist
        if (!file_exists($path_movies)) {
            mkdir($path_movies, 0777, true);
        }

        // nb of frames
        $nb_frames = 100;
        // frame width
        $width = 1680;
        // frame height
        $height = 2160;

        // load font
        $path_media = gaia::kv("path_media");

        $font = "$path_media/fonts/RobotoMono-Light.ttf";
        $font_title = "$path_media/fonts/Monoton-Regular.ttf";
        gaia::kv("movie/font", $font);
        gaia::kv("movie/font_title", $font_title);
        gaia::kv("movie/font_size", $font_size);

        // loop and build frames
        foreach(range(0, $nb_frames-1) as $frame_index)
        {
            
            // get the frame file path
            $frame_file = "$path_movies/f-$frame_index.webp";

            // debug
            echo "frame($title,$frame_index) $frame_file".PHP_EOL;


            // write the frame index
            // write text with ttf font
            $slide_title = $slides[$frame_index]["title"] ?? $title ?? "";
            $slide_content = $slides[$frame_index]["content"] ?? "";

            // trim title
            $slide_title = trim($slide_title, "# ");

            $page_number = $frame_index+1;

            $text = <<<txt

            $slide_content

            txt;

            // ---------*---------*---------*---------*---------*

            movie::text_center($frame_file, $slide_title, $text, $page_number, $width, $height);

        }

        // destroy earth image
        $earth = gaia::kv("movie/earth");
        if ($earth) imagedestroy($earth);

        return $path_movies;
    }

    static function text_center ($frame_file, $title, $text, $footer, $width, $height)
    {
        static $earth = null;
        if (!$earth) {
            // fill the frame with image pages/earth-night.jpg
            $path_data = gaia::kv("path_data");
            $earth_path = "$path_data/pages/earth-night.jpg";
            $earth = imagecreatefromjpeg($earth_path);

            gaia::kv("movie/earth", $earth);
        }

        // create the frame
        $frame = imagecreatetruecolor($width, $height);

        $transparent = imagecolorallocatealpha($frame, 255, 255, 255, 127);
        $trans100 = imagecolorallocatealpha($frame, 0, 0, 0, 100);
        $black = imagecolorallocatealpha($frame, 0, 0, 0, 0);
        $white = imagecolorallocatealpha($frame, 255, 255, 255, 0);
        $red = imagecolorallocatealpha($frame, 255, 0, 0, 0);
        
        // fill the frame with a color
        // imagefill($frame, 0, 0, $transparent);
        // imagefill($frame, 0, 0, $black);

        // imagecopy($frame, $earth, 0, 0, 0, 0, 1680, 2160);
        // image copy resampled with full width 
        if ($earth) {
            $earth_w = imagesx($earth);
            $earth_h = imagesy($earth);
            $earth_ratio = $earth_h / $earth_w;
            $dst_h = $width * $earth_ratio;

            imagecopyresampled($frame, $earth, 0, 0, 0, 0, $width, $dst_h, $earth_w, $earth_h);    
            imagefill($frame, 0, 0, $trans100);
        }

        $font = gaia::kv("movie/font");
        $font_title = gaia::kv("movie/font_title");
        $font_size = gaia::kv("movie/font_size");
        $width = imagesx($frame);
        $height = imagesy($frame);

        $points = imagettfbbox($font_size, 0, $font, $text);
        $text_width = $points[2];
        $text_height = $points[3];
        // $text_x = round(0.5 * ($width - $text_width));
        // $text_y = round(0.5 * ($height - $text_height));
        
        $text_x = 2 * $font_size;

        // title
        imagettftext($frame, $font_size+10, 0, $text_x, 4*$font_size, $red, $font_title, $title);
        // content
        imagettftext($frame, $font_size, 0, $text_x, 6*$font_size, $white, $font, $text);
        // footer
        imagettftext($frame, $font_size-10, 0, $text_x, $height-2*$font_size, $white, $font, $footer);

        // save alpha channel
        imagesavealpha($frame, true);
        // save the frame
        // WARNING: webp frames are bad for ffmpeg
        imagewebp($frame, $frame_file);
        // free the frame
        imagedestroy($frame);
    }

    static function build_movie ()
    {
        // load config from json parameter file
        extract(cli::param_json(2));
        // get the file name
        // important for youtube as it will be the title
        // and youtube keeps the original filename
        $file ??= "movie";
        $markdown ??= "";
        if ($markdown) {
            // store the filename
            gaia::kv("os/markdown/file", $markdown);
            os::markdown();
        }

        $path_frames = movie::build_frames();

        // call ffmpeg to build the movie
        $path_data = gaia::kv("path_data");
        $now = date("ymd-His");
        $path_movie = "$path_data/movies/$file-$now.webm";
        // $cmd = "ffmpeg -framerate 1/6 -i $path_frames/f-%d.png -c:v libx264 -profile:v high -crf 20 -pix_fmt yuv420p $path_movie";
        $cmd = "ffmpeg -framerate 1/6 -i $path_frames/f-%d.webp -crf 20 $path_movie";
        shell_exec($cmd);
    }

    //@end_class
}

//@end_file
