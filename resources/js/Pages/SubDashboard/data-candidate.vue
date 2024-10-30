<script setup lang="ts">
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    title: {
        type: String,
    },
});
</script>

<template>
    <Head :title="title" />
    <Sidebar>
        <div>
            <h1>Data candidate</h1>
        </div>
    </Sidebar>
</template>
