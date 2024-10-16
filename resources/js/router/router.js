import { createRouter, createWebHistory } from "vue-router";

// Import your components
import LandingPage from "../Pages/LandingPage.vue";
import ProductPage from "../Layouts/Landing-page/sub-pages/ProductPage.vue";
import FeaturesPage from "../Layouts/Landing-page/sub-pages/FeaturesPage.vue";
import CompanyPage from "../Layouts/Landing-page/sub-pages/CompanyPage.vue";

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
