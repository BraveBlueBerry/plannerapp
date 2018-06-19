<template>
    <div>
        <h1 id="appName">Planner App</h1>
        <p>
            <router-link :to="{ name: 'home' }">Home</router-link> |
            <router-link :to="{ name: 'tasks.index' }">Tasks</router-link>
        </p>
        <div class="container">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
    export default {}
</script>
