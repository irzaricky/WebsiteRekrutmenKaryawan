<template>
    <div>
        <Head :title="title" />
        <BaseHeader />
        <LandingPageHero />
        <LandingPageFeature />
        <LandingPageTeams />
        <BaseFooter />
    </div>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import BaseHeader from "../components/BaseHeader.vue";
import BaseFooter from "../components/BaseFooter.vue";
import LandingPageHero from "../Layouts/Landing-page/LandingPage-hero.vue";
import LandingPageFeature from "../Layouts/Landing-page/LandingPage-FeatureSection.vue";
import LandingPageTeams from "../Layouts/Landing-page/LandingPage-teams.vue";

defineProps({
    title: {
        type: String,
    },
});
</script>
