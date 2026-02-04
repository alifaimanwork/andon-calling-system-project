// Echo
import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    activityTimeout: 10000,
    forceTLS: false,
    wsHost: window.location.hostname,
    wsPort: 80,
    enabledTransports: ['ws']

});