<?php

$path_media = gaia::kv("web/media");
$extention = gaia::kv("web/media/extension");

if (is_file($path_media)) {
    // send file
    $mime = web::mime($extension);
    header("Content-Type: $mime");
    readfile($path_media);
}
