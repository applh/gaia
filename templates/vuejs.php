<?php

$uri = $_SERVER["REQUEST_URI"] ?? "";

$vue_component =
<<<js

console.log('$uri');

let indata = {};
let template = `
<h1>HELLO</h1>
<h3>$uri</h3>  
`;
// vue js async component
export default {
    template,
    data() {
        return indata;
    },
}

js;

header("Content-Type: application/javascript");

echo $vue_component;
