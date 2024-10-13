import "./bootstrap";
import { createApp } from "vue";
import landingPagehero from "./components/LandingPage-hero.vue";
import landingPagefeature from "./components/LandingPage-FeatureSection.vue";
import landingpagepricing from "./components/LandingPage-pricing.vue";
import landingpageheader from "./components/LandingPage-header.vue";

createApp(landingPagehero).mount("#LandingPage-hero");
createApp(landingPagefeature).mount("#LandingPage-feature");
createApp(landingpagepricing).mount("#LandingPage-pricing");
createApp(landingpageheader).mount("#LandingPage-header");