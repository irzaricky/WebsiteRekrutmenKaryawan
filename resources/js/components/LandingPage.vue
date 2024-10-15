<template>
    <div>
        <BaseHeader />
        <LandingPageHero />
        <LandingPageFeature />
        <LandingPageHeader />
        <LandingPageTeams />
        <BaseFooter />
    </div>
</template>

<script>
import BaseHeader from "./BaseHeader.vue";
import BaseFooter from "./BaseFooter.vue";
import LandingPageHeader from "./Landing-page/LandingPage-header.vue";
import LandingPageHero from "./Landing-page/LandingPage-hero.vue";
import LandingPageFeature from "./Landing-page/LandingPage-FeatureSection.vue";
import LandingPageTeams from "./Landing-page/LandingPage-teams.vue";

export default {
    components: {
        LandingPageHeader,
        LandingPageHero,
        LandingPageFeature,
        LandingPageTeams,
        BaseHeader,
        BaseFooter,
    },
};
</script>
