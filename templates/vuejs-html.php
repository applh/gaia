<!-- VUEJS CONTAINER -->
<div id="appContainer"></div>

<!-- VUEJS TEMPLATE -->
<template id="appTemplate" data-compos="box-sm box-md box-lg box-xl">
    <section>
        <p class="pad4">{{ message }}</p>
    </section>
    <av-box-sm v-if="window_w < 800"></av-box-sm>
    <av-box-md v-else-if="window_w < 1600"></av-box-md>
    <av-box-lg v-else-if="window_w < 2400"></av-box-lg>
    <av-box-xl v-else></av-box-xl>
</template>

<!-- VUEJS INIT -->
<script type="module">
</script>