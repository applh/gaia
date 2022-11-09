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
        // loop and build frames
        foreach(range(1, $nb_frames) as $frame_index)
        {
            
            // get the frame file path
            $frame_file = "$path_movies/f-$frame_index.png";

            // debug
            echo "frame($title,$frame_index) $frame_file".PHP_EOL;

            // create the frame
            $frame = imagecreatetruecolor($width, $height);
            // fill the frame with a color
            $transparent = imagecolorallocatealpha($frame, 255, 255, 255, 127);
            imagefill($frame, 0, 0, $transparent);
            // write the frame index
            $red = imagecolorallocatealpha($frame, 255, 0, 0, 0);
            // write text with ttf font
            $text = "$title $frame_index";
            $font_size = 100;
            $text_width = imagettfbbox($font_size, 0, $font, $text)[2];
            $text_height = imagettfbbox($font_size, 0, $font, $text)[3];
            $text_x = round(0.5 * ($width - $text_width));
            $text_y = round(0.5 * ($height - $text_height));
            imagettftext($frame, $font_size, 0, $text_x, $text_y, $red, $font, $text);

            // save alpha channel
            imagesavealpha($frame, true);
            // save the frame
            imagepng($frame, $frame_file);
            // free the frame
            imagedestroy($frame);
        }
        return $path_movies;
    }

    static function build_movie ()
    {
        // load config from json parameter file
        extract(cli::param_json(2));
        // get the file name
        // important for youtube as it will be the title
        // and youtube keeps the original filename
        $file ??= "movie";

        $path_frames = movie::build_frames();

        // call ffmpeg to build the movie
        $path_data = gaia::kv("path_data");
        $now = date("ymd-His");
        $path_movie = "$path_data/movies/$file-$now.mp4";
        $cmd = "ffmpeg -framerate 1/6 -i $path_frames/f-%d.png -c:v libx264 -profile:v high -crf 20 -pix_fmt yuv420p $path_movie";
        shell_exec($cmd);
    }

    //@end_class
}

//@end_file
