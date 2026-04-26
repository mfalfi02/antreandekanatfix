{{-- Skrip PWA ini mengatur tombol install dan service worker agar aplikasi bisa dipasang ke perangkat --}}
<div id="pwa-install-shell"
    style="position:fixed;right:16px;bottom:16px;z-index:9999;display:none;max-width:min(92vw,360px);">
    <button id="pwa-install-btn" type="button"
        style="display:inline-flex;align-items:center;gap:10px;padding:12px 16px;border:none;border-radius:999px;background:linear-gradient(135deg,#0f60f0,#2563eb);color:#fff;box-shadow:0 18px 36px -18px rgba(15,23,42,.55);font-weight:700;letter-spacing:.01em;cursor:pointer;">
        <span style="display:inline-flex;width:2rem;height:2rem;align-items:center;justify-content:center;border-radius:999px;background:rgba(255,255,255,.16);font-size:1rem;line-height:1;">+</span>
        <span>Install App</span>
    </button>
</div>

<script>
    (() => {
        const shell = document.getElementById('pwa-install-shell');
        const button = document.getElementById('pwa-install-btn');

        if (!shell || !button) {
            return;
        }

        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

        if (isStandalone) {
            return;
        }

        let deferredPrompt = null;

        const showButton = () => {
            shell.style.display = 'block';
        };

        const hideButton = () => {
            shell.style.display = 'none';
        };

        hideButton();

        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            deferredPrompt = event;
            showButton();
        });

        window.addEventListener('appinstalled', () => {
            deferredPrompt = null;
            hideButton();
        });

        button.addEventListener('click', async () => {
            if (!deferredPrompt) {
                return;
            }

            deferredPrompt.prompt();
            const choiceResult = await deferredPrompt.userChoice;
            deferredPrompt = null;

            if (choiceResult.outcome === 'accepted') {
                hideButton();
            }
        });

        window.addEventListener('load', () => {
            if (!deferredPrompt) {
                hideButton();
            }
        });
    })();

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('{{ asset('sw.js') }}')
                .catch((error) => {
                    console.error('PWA service worker registration failed:', error);
                });
        });
    }
</script>
