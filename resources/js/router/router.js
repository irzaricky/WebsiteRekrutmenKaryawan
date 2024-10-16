import { createRouter, createWebHistory } from "vue-router";

// Import your components
import LandingPage from "../Pages/LandingPage.vue";
import ProductPage from "../Pages/landing-subpages/ProductPage.vue";
import FeaturesPage from "../Pages/landing-subpages/FeaturesPage.vue";
import CompanyPage from "../Pages/landing-subpages/CompanyPage.vue";

// Define your routes
const routes = [
    { path: "/", component: LandingPage }, // Render LandingPage on root path
    { path: "/product", name: "Product", component: ProductPage },
    { path: "/features", name: "Features", component: FeaturesPage },
    { path: "/company", name: "Company", component: CompanyPage },
];

// Create the router instance
const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
