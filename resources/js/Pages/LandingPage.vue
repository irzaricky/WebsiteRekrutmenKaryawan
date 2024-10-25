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
import BaseHeader from "../components/BaseHeader.vue";
import BaseFooter from "../components/BaseFooter.vue";
import LandingPageHeader from "../Layouts/Landing-page/LandingPage-header.vue";
import LandingPageHero from "../Layouts/Landing-page/LandingPage-hero.vue";
import LandingPageFeature from "../Layouts/Landing-page/LandingPage-FeatureSection.vue";
import LandingPageTeams from "../Layouts/Landing-page/LandingPage-teams.vue";

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
