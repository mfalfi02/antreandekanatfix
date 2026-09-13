const VERSION = 'v3';
const CACHE_PREFIX = 'antrean-dekanat-pwa';
const PAGE_CACHE = `${CACHE_PREFIX}-pages-${VERSION}`;
const ASSET_CACHE = `${CACHE_PREFIX}-assets-${VERSION}`;
const OFFLINE_URL = '/offline.html';

const PRECACHE_URLS = [
    OFFLINE_URL,
    '/manifest.json',
    '/favicon.ico',
    '/images/Ruang1.png',
    '/images/logosistem.jpeg',
    '/pwa/dashboard-icons/icon-16x16.png',
    '/pwa/dashboard-icons/icon-32x32.png',
    '/pwa/dashboard-icons/icon-180x180.png',
    '/pwa/icons/icon-16x16.png',
    '/pwa/icons/icon-32x32.png',
    '/pwa/icons/icon-64x64.png',
    '/pwa/icons/icon-72x72.png',
    '/pwa/icons/icon-96x96.png',
    '/pwa/icons/icon-128x128.png',
    '/pwa/icons/icon-144x144.png',
    '/pwa/icons/icon-152x152.png',
    '/pwa/icons/icon-180x180.png',
    '/pwa/icons/icon-192x192.png',
    '/pwa/icons/icon-384x384.png',
    '/pwa/icons/icon-512x512.png'
];

const PUBLIC_NAVIGATION_PATHS = ['/', '/display', '/login'];
const PRIVATE_NAVIGATION_PREFIXES = ['/admin', '/dashboard', '/mahasiswa', '/users', '/services', '/reports', '/queue'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(ASSET_CACHE).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => ![PAGE_CACHE, ASSET_CACHE].includes(key))
                    .map((key) => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

function isPublicNavigation(pathname) {
    return PUBLIC_NAVIGATION_PATHS.includes(pathname);
}

function isPrivateNavigation(pathname) {
    return PRIVATE_NAVIGATION_PREFIXES.some((prefix) => pathname === prefix || pathname.startsWith(`${prefix}/`));
}

async function cacheStaticAsset(request) {
    const cached = await caches.match(request);

    const networkResponse = fetch(request).then(async (response) => {
        if (response && response.ok) {
            const cache = await caches.open(ASSET_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    }).catch(() => cached);

    return cached || networkResponse;
}

async function cachePublicPage(request) {
    try {
        const response = await fetch(request);
        if (response && response.ok) {
            const cache = await caches.open(PAGE_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        const cachedPage = await caches.match(request);
        return cachedPage || caches.match(OFFLINE_URL);
    }
}

async function loadPrivatePage(request) {
    try {
        return await fetch(request);
    } catch (error) {
        return (await caches.match(OFFLINE_URL)) || Response.error();
    }
}

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    const requestUrl = new URL(event.request.url);

    if (requestUrl.origin !== self.location.origin) {
        return;
    }

    if (event.request.mode === 'navigate') {
        if (isPublicNavigation(requestUrl.pathname)) {
            event.respondWith(cachePublicPage(event.request));
            return;
        }

        if (isPrivateNavigation(requestUrl.pathname)) {
            event.respondWith(loadPrivatePage(event.request));
            return;
        }

        event.respondWith(loadPrivatePage(event.request));
        return;
    }

    const isStaticAsset = ['style', 'script', 'image', 'font'].includes(event.request.destination)
        || requestUrl.pathname.startsWith('/build/')
        || requestUrl.pathname.startsWith('/images/')
        || requestUrl.pathname.startsWith('/pwa/');

    if (isStaticAsset) {
        event.respondWith(cacheStaticAsset(event.request));
    }
});
