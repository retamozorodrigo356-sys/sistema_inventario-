/*
 * Actualización en tiempo real del dashboard mediante Laravel Reverb.
 * Cuando otro usuario registra una entrada, salida o cambia un producto,
 * se recibe el evento y se recarga el panel sin refrescar el navegador.
 *
 * Requiere que la vista defina window.reverbConfig y cargue laravel-echo
 * y pusher-js (ver resources/views/dashboard.blade.php).
 */
(function () {
    'use strict';

    const dashboard = document.getElementById('dashboard-live');
    if (!dashboard) return;

    const indicator = document.getElementById('live-indicator');
    const status = document.getElementById('live-status');
    const config = window.reverbConfig || {};

    const setStatus = (text, colorClass) => {
        if (status) {
            status.textContent = text;
            status.className = 'small fw-semibold ' + colorClass;
        }
        if (indicator) {
            indicator.dataset.state = colorClass === 'text-success' ? 'online' : 'offline';
        }
    };

    let refreshTimer = null;

    const scheduleRefresh = () => {
        clearTimeout(refreshTimer);
        refreshTimer = setTimeout(refreshDashboard, 400);
    };

    async function refreshDashboard() {
        try {
            const response = await fetch(dashboard.dataset.url, {
                headers: { Accept: 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });

            if (!response.ok) throw new Error('HTTP ' + response.status);

            const html = await response.text();
            const fresh = new DOMParser()
                .parseFromString(html, 'text/html')
                .getElementById('dashboard-live');

            if (fresh) {
                dashboard.innerHTML = fresh.innerHTML;
            }

            setStatus('En vivo', 'text-success');
        } catch (error) {
            console.error('No se pudo actualizar el dashboard en tiempo real.', error);
            setStatus('Actualización pendiente', 'text-warning');
        }
    }

    function showToast(event) {
        const container = document.getElementById('live-toasts');
        if (!container || !window.bootstrap) return;

        const toast = document.createElement('div');
        toast.className = 'toast align-items-center border-0 shadow';
        toast.setAttribute('role', 'status');
        toast.setAttribute('aria-live', 'polite');
        toast.innerHTML =
            '<div class="d-flex">' +
            '<div class="toast-body"><i class="bi bi-broadcast text-primary me-2"></i><span class="live-toast-text"></span></div>' +
            '<button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>' +
            '</div>';

        toast.querySelector('.live-toast-text').textContent = event.actor
            ? event.description + ' · ' + event.actor
            : event.description;

        container.appendChild(toast);

        const instance = new window.bootstrap.Toast(toast, { delay: 5000 });
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
        instance.show();
    }

    if (!window.Echo || !config.key) {
        setStatus('Tiempo real no configurado', 'text-secondary');
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const echo = new window.Echo({
        broadcaster: 'reverb',
        key: config.key,
        wsHost: config.host,
        wsPort: config.port,
        wssPort: config.port,
        forceTLS: config.scheme === 'https',
        enabledTransports: ['ws', 'wss'],
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
        },
    });

    const connection = echo.connector?.pusher?.connection;

    connection?.bind('connected', () => setStatus('En vivo', 'text-success'));
    connection?.bind('disconnected', () => setStatus('Sin conexión', 'text-danger'));
    connection?.bind('unavailable', () => setStatus('Sin conexión', 'text-danger'));
    connection?.bind('failed', () => setStatus('Sin conexión', 'text-danger'));

    echo.private('inventory').listen('.inventory.changed', (event) => {
        showToast(event);
        scheduleRefresh();
    });
})();
