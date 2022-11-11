console.log('vue-mixin.js');

let mixin = {
    created() {
        console.log('mixin created');
    },
    mounted() {
        console.log('mixin mounted');
    }
}

export default {
    mixin,
}