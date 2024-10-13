import "./bootstrap";
import "../css/app.css";
import { createApp } from "vue";
import router from "./router/router"; // Import the router configuration
import App from "./App.vue"; // Import your main App.vue

// Create the app and use the router
const app = createApp(App);
app.use(router); // Use Vue Router in your app
app.mount("#app"); // Mount the app to the main element in your HTML
