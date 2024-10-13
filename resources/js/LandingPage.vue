<template>
    <div>
        <LandingPageHero />
        <LandingPageFeature />
        <LandingPagePricing />
        <LandingPageHeader />
        <LandingPageTeams />
    </div>
</template>

<script>
import LandingPageHeader from "./components/Landing-page/LandingPage-header.vue";
import LandingPageHero from "./components/Landing-page/LandingPage-hero.vue";
import LandingPageFeature from "./components/Landing-page/LandingPage-FeatureSection.vue";
import LandingPagePricing from "./components/Landing-page/LandingPage-pricing.vue";
import LandingPageTeams from "./components/Landing-page/LandingPage-teams.vue";

export default {
    components: {
        LandingPageHeader,
        LandingPageHero,
        LandingPageFeature,
        LandingPagePricing,
        LandingPageTeams,
    },
};
</script>
