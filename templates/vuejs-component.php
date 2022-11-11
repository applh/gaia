<?php

$uri = $_SERVER["REQUEST_URI"] ?? "";
extract(parse_url($uri));
$path = $path ?? "";
extract(pathinfo($path));
$filename ??= "";

$vue_component =
<<<js

console.log('$filename');

// TEST MIXINS

import { default as titi } from '/media/vue-mixin.js';

const toto = {
    created() {
      console.log('toto created');
    }
}

let mixins = [ toto, titi.mixin ];

// ADD COMPO CSS

let css_compo = `
.$filename {
    color: red;
    padding: 2rem;
}

`;

// append the css to the head
let head = document.head;
let style = document.createElement('style');
style.type = 'text/css';
style.appendChild(document.createTextNode(css_compo));
head.appendChild(style);


// COMPO TEMPLATE

let template = `
<div class="$filename row">
    <button @click="counter++">{{ counter }}</button>
</div>
`;

// COMPO SCRIPT

let data_compo = {
    counter: 0,
};

let inject = [ 'avroot' ];

let setup = function () {
    console.log('setup');
};

let created = function () {
    console.log('created');
};

let mounted = function () {
    console.log('mounted');
    // call the root method
    this.avroot.test('$filename');
};

// vue js async component
export default {
    template,
    inject,
    data: () => data_compo,
    mixins,
    setup,
    created,
    mounted,
}

js;

header("Content-Type: application/javascript");

echo $vue_component;
