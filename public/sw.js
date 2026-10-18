/* EcoJourney service worker
   - Precaches the offline fallback + PWA icons.
   - Navigations: network-first with offline fallback.
   - Static Vite build assets: stale-while-revalidate.
   - External images/CDN: cache-first with generous cap.
   - API + /sw.js are never intercepted (auth must always hit the network). */

const VERSION = 'ecojourney-v4';
const STATIC_CACHE = `${VERSION}-static`;
const RUNTIME_CACHE = `${VERSION}-runtime`;
const OFFLINE_URL = '/offline.html';

const PRECACHE_URLS = [
    OFFLINE_URL,
    '/manifest.webmanifest',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/maskable-512.png',
    '/icons/apple-touch-icon.png',
];

const IMAGE_CACHE = `${VERSION}-images`;
const MAX_IMAGE_ENTRIES = 80;

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

async function purgeStrayApiEntries() {
    const names = await caches.keys();

    await Promise.all(names.map(async (name) => {
        const cache = await caches.open(name);
        const requests = await cache.keys();

        await Promise.all(requests
            .filter((request) => new URL(request.url).pathname.startsWith('/api/'))
            .map((request) => cache.delete(request)));
    }));
}

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => !key.startsWith(VERSION))
                    .map((key) => caches.delete(key))
            ))
            // Belt and suspenders: older builds cached /api/auth/user and made
            // a logged-out reload look signed-in again.
            .then(() => purgeStrayApiEntries())
            .then(() => self.clients.claim())
    );
});

self.addEventListener('message', (event) => {
    if (event.data === 'skip-waiting') self.skipWaiting();
});

function isStaticAsset(url) {
    return url.pathname.startsWith('/build/') || url.pathname.startsWith('/icons/');
}

function isImageRequest(request, url) {
    return request.destination === 'image'
        || url.hostname === 'images.unsplash.com'
        || (url.hostname.endsWith('.tile.openstreetmap.org'))
        || url.hostname === 'a.tile.openstreetmap.org'
        || url.hostname === 'b.tile.openstreetmap.org'
        || url.hostname === 'c.tile.openstreetmap.org'
        || url.pathname.startsWith('/images/');
}

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response && response.ok) {
            const cache = await caches.open(RUNTIME_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        return cached || Response.error();
    }
}

async function networkFirstNavigation(request) {
    try {
        const response = await fetch(request);
        // Admin pages are auth-gated — never store them, so a logged-out
        // session on a shared device cannot replay cached admin markup offline.
        if (response && response.ok && !new URL(request.url).pathname.startsWith('/admin')) {
            const cache = await caches.open(RUNTIME_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        const cached = await caches.match(request);
        if (cached) return cached;
        const offline = await caches.match(OFFLINE_URL);
        if (offline) return offline;
        return new Response('Offline', { status: 503, statusText: 'Offline' });
    }
}

async function staleWhileRevalidate(request) {
    const cache = await caches.open(STATIC_CACHE);
    const cached = await cache.match(request);

    const network = fetch(request)
        .then((response) => {
            if (response && response.ok) cache.put(request, response.clone());
            return response;
        })
        .catch(() => cached);

    return cached || network;
}

async function imageCacheFirst(request) {
    const cache = await caches.open(IMAGE_CACHE);
    const cached = await cache.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response && response.ok) {
            cache.put(request, response.clone());
            const keys = await cache.keys();
            if (keys.length > MAX_IMAGE_ENTRIES) {
                await cache.delete(keys[0]);
            }
        }
        return response;
    } catch (error) {
        return cached || Response.error();
    }
}

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // These guards run first and unconditionally: auth state (e.g.
    // /api/auth/user) must always come from the network, otherwise logout
    // does not survive a reload. The service worker script itself must also
    // stay uncached so browsers can pick up new versions.
    if (request.method !== 'GET') return;
    if (url.pathname === '/sw.js') return;
    if (url.origin === self.location.origin && url.pathname.startsWith('/api/')) return;

    if (url.origin !== self.location.origin && !isImageRequest(request, url)) return;

    if (request.mode === 'navigate') {
        event.respondWith(networkFirstNavigation(request));
        return;
    }

    if (isStaticAsset(url)) {
        event.respondWith(staleWhileRevalidate(request));
        return;
    }

    if (isImageRequest(request, url)) {
        event.respondWith(imageCacheFirst(request));
        return;
    }

    if (url.origin === self.location.origin) {
        // Same hardening for same-origin GETs that are not API/navigations.
        if (url.pathname.startsWith('/admin')) return;
        event.respondWith(cacheFirst(request));
    }
});
