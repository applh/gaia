<?php

$uri = $_SERVER["REQUEST_URI"] ?? "";

$vue_component =
<<<js

console.log('$uri');

let template = `
<h1>$uri</h1>
`;

let indata = {

};

let inject = [ 'avroot' ];

let created = function () {
    console.log('created');
};

let mounted = function () {
    console.log('mounted');
    // call the root method
    this.avroot.test('$uri');
};

// vue js async component
export default {
    template,
    inject,
    data: () => indata,
    created,
    mounted,
}

js;

header("Content-Type: application/javascript");

echo $vue_component;
