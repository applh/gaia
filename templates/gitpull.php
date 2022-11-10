<?php

$infos = [];

// path root
$path_root = gaia::kv("root");
// change local path and execute git pull
chdir($path_root);
$cmd = "git pull";
$output = shell_exec($cmd);

// return json data
$infos["now"] = $now;
$infos["uri"] = $uri;
$infos["output"] = $output;

// return json data
header("Content-Type: application/json");
echo json_encode($infos, JSON_PRETTY_PRINT);
