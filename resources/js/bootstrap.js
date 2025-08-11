import * as Popper from "@popperjs/core"; // For Bootstrap dropdowns/tooltips
window.Popper = Popper;

import "bootstrap"; // For Bootstrap's JavaScript components

import axios from "axios"; // For making HTTP requests
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time features.
 */

import Echo from "laravel-echo"; // For real-time functionality

import Pusher from "pusher-js"; // The Pusher client library (Soketi emulates Pusher)
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY, // Use import.meta.env for Vite
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: import.meta.env.VITE_PUSHER_HOST,
    wsPort: import.meta.env.VITE_PUSHER_PORT,
    wssPort: null,
    forceTLS: false,
    enabledTransports: ["ws"], // Crucial for production in some environments
});

// Optional: Echo connection status logging for debugging
window.Echo.connector.pusher.connection.bind("connected", () => {
    console.log("Echo connected to WebSocket server!");
});
window.Echo.connector.pusher.connection.bind("disconnected", () => {
    console.warn("Echo disconnected from WebSocket server!");
});
window.Echo.connector.pusher.connection.bind("error", (err) => {
    console.error("Echo connection error:", err);
});
