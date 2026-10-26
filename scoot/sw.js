const CACHE_NAME = 'scoot-v3';
const SHELL_ASSETS = [
    'assets/style.css',
    'assets/app.js',
    'assets/img/logo.png',
    'assets/img/rider.png',
    'assets/vendor/leaflet/leaflet.js',
    'assets/vendor/leaflet/leaflet.css',
    'assets/vendor/jsQR.js',
    'manifest.json',
    'offline.html'
];

// Install Event: Cache Asset Utama
self.addEventListener('install', (e) => {
    e.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(SHELL_ASSETS))
    );
    self.skipWaiting();
});

// Activate Event: Hapus Cache Versi Lama
self.addEventListener('activate', (e) => {
    e.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

// Fetch Event: Pengelolaan Request & Offline Fallback
self.addEventListener('fetch', (e) => {
    const request = e.request;
    const url = new URL(request.url);

    if (request.method !== 'GET') return;

    // Navigasi Halaman Utama (Network First -> Offline Page)
    if (request.mode === 'navigate') {
        e.respondWith(
            fetch(request).catch(() => caches.match('offline.html'))
        );
        return;
    }

    // Asset Static Latar Belakang (Network First -> Cache Fallback)
    if (url.origin === location.origin) {
        if (!url.pathname.includes('/assets/') && !url.pathname.endsWith('manifest.json')) return;

        e.respondWith(
            fetch(request)
                .then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    }
                    return response;
                })
                .catch(() => caches.match(request))
        );
        return;
    }

    // Cache Font Google (Stale While Revalidate)
    if (/fonts\.(googleapis|gstatic)/.test(url.host)) {
        e.respondWith(
            caches.match(request).then((cachedResponse) => {
                return (
                    cachedResponse ||
                    fetch(request).then((response) => {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                        return response;
                    })
                );
            })
        );
    }
});