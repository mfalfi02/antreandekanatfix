// Axios dipakai untuk request AJAX global di aplikasi.
import axios from 'axios';
window.axios = axios;

// Header default supaya request dikenali sebagai XMLHttpRequest.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Inisialisasi Laravel Echo untuk event realtime dari server.
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Konfigurasi websocket mengikuti env yang dipasang di project.
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST ?? window.location.hostname,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
