console.log('hello');

// store my reactive data
let appData = {
    window_w: window.innerWidth,
    window_h: window.innerHeight,
    message: 'Vue is everywhere!'
}

let created = function () {
    console.log('created');

    // WARNING: REGISTER COMPONENTS BEFORE MOUNTING
    ['admin-sm', 'admin-md', 'admin-lg', 'admin-xl', 'form', 'test', 'titi', 'tutu']
        .forEach(function (name) {
            app.component(
                'av-' + name,
                vue.defineAsyncComponent(() => import(`/vue/av-${name}.js`))
            );
        });
}

let mounted = function () {
    console.log('mounted');

    // add resize event listener
    window.addEventListener('resize', () => {
        this.window_w = window.innerWidth;
        this.window_h = window.innerHeight;
        this.message = '' + this.window_w + 'x' + this.window_h;
    });

    this.test('test1')
}

let methods = {
    test (msg='') {
        console.log('HELLO FROM APP: ' + msg);
    }
}

// add vuejs app from CDN
import * as vue
    from "/media/vue.esm-browser.min.js";

const compoApp = vue.defineComponent({
    template: "#appTemplate",
    data: () => appData,
    provide() {
        return {
            // tricky way to pass 'this' to child components
            avroot: this,
        }
    },
    methods,
    created,
    mounted,
});

let app = vue.createApp(compoApp);
app.mount("#appContainer");
