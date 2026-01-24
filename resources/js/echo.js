import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const scheme = import.meta.env.VITE_REVERB_SCHEME ?? 'http';
const isSecure = scheme === 'https';
const wsHost = import.meta.env.VITE_REVERB_HOST ?? window.location.hostname;
const wsPort = import.meta.env.VITE_REVERB_PORT ?? (isSecure ? 443 : 8080);

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: wsHost,
    wsPort: wsPort,
    wssPort: wsPort,
    forceTLS: isSecure,
    enabledTransports: isSecure ? ['wss', 'ws'] : ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    },
    disableStats: true,
    cluster: '',
    encrypted: isSecure,
});

const channel = window.Echo.channel('public-announcements');

channel.error((error) => {
    console.error('Channel subscription error:', error);
});

channel.listen('.UserRegistered', (event) => {
    window.dispatchEvent(new CustomEvent('user-registered', {
        detail: event
    }));
});