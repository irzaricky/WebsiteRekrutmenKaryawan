<template>
    <BaseHeader />
    <router-view></router-view>
    <BaseFooter />
</template>

<script>
import BaseHeader from "./components/BaseHeader.vue";
import BaseFooter from "./components/BaseFooter.vue";

export default {
    name: "App",
    components: {
        BaseHeader,
        BaseFooter,
    },
};
</script>

<style></style>
