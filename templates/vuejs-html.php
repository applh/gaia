<!-- VUEJS CONTAINER -->
<div id="appContainer"></div>

<!-- VUEJS TEMPLATE -->
<template id="appTemplate">
    <h3>{{ message }}</h3>
    <av-admin-sm v-if="window_w < 800"></av-admin-sm>
    <av-admin-md v-else-if="window_w < 1600"></av-admin-md>
    <av-admin-lg v-else-if="window_w < 2400"></av-admin-lg>
    <av-admin-xl v-else></av-admin-xl>
</template>

<template id="temp">
    <h3>{{ message }}</h3>
    <av-admin-sm v-if="window_w < 800"></av-admin-sm>
    <av-admin-md v-else-if="window_w < 1600"></av-admin-md>
    <av-admin-lg v-else-if="window_w < 2400"></av-admin-lg>
    <av-admin-xl v-else></av-admin-xl>
</template>

<!-- VUEJS INIT -->
<script type="module">
</script>