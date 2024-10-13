import { createRouter, createWebHistory } from "vue-router";

// Import your components
import LandingPage from "../components/LandingPage.vue";
import ProductPage from "../components/Landing-page/sub-pages/ProductPage.vue";
import FeaturesPage from "../components/Landing-page/sub-pages/FeaturesPage.vue";
import MarketplacePage from "../components/Landing-page/sub-pages/MarketplacePage.vue";
import CompanyPage from "../components/Landing-page/sub-pages/CompanyPage.vue";

// Define your routes
const routes = [
    { path: "/", component: LandingPage }, // Render LandingPage on root path
    { path: "/product", name: "Product", component: ProductPage },
    { path: "/features", name: "Features", component: FeaturesPage },
    { path: "/marketplace", name: "Marketplace", component: MarketplacePage },
    { path: "/company", name: "Company", component: CompanyPage },
];

// Create the router instance
const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
