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
import LandingPageHeader from "./Landing-page/LandingPage-header.vue";
import LandingPageHero from "./Landing-page/LandingPage-hero.vue";
import LandingPageFeature from "./Landing-page/LandingPage-FeatureSection.vue";
import LandingPagePricing from "./Landing-page/LandingPage-pricing.vue";
import LandingPageTeams from "./Landing-page/LandingPage-teams.vue";

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
