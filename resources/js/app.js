import './bootstrap';
import { createApp, watch } from 'vue';
import App from './App.vue';
import router from './router';
import { applyLocaleToDocument, t } from './i18n';
import { consent } from './state/consent';

const app = createApp(App);

app.config.globalProperties.$t = t;

applyLocaleToDocument();

app.use(router).mount('#app');

function loadAnalytics() {
    // Tempel script GA / Plausible di sini. Hanya dipanggil kalau user klik "Accept all".
}

watch(() => consent.value, (value) => {
    if (value === 'accepted') {
        loadAnalytics();
    }
}, { immediate: true });

if ('serviceWorker' in navigator && import.meta.env.PROD) {
    window.addEventListener('load', () => {
        navigator.serviceWorker
            .register('/sw.js', { scope: '/' })
            .then((registration) => {
                // Promote a waiting worker as soon as one is available.
                registration.addEventListener('updatefound', () => {
                    const installing = registration.installing;
                    if (!installing) return;
                    installing.addEventListener('statechange', () => {
                        if (installing.state === 'installed' && navigator.serviceWorker.controller) {
                            installing.postMessage('skip-waiting');
                        }
                    });
                });
            })
            .catch(() => {
                // SW registration failure is non-fatal; app keeps working.
            });
    });
}
