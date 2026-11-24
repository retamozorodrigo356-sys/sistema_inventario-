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

    const EchoConstructor = typeof window.Echo === 'function'
        ? window.Echo
        : (window.Echo?.default || window.Echo);

    if (!EchoConstructor || !config.key) {
        setStatus('Tiempo real no configurado', 'text-secondary');
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // Usar el host actual de la barra de direcciones para evitar discrepancias (localhost vs 127.0.0.1)
    const wsHost = (window.location.hostname && window.location.hostname !== '')
        ? window.location.hostname
        : (config.host || 'localhost');

    const echo = new EchoConstructor({
        broadcaster: 'reverb',
        key: config.key,
        wsHost: wsHost,
        wsPort: config.port ? parseInt(config.port, 10) : 8080,
        wssPort: config.port ? parseInt(config.port, 10) : 8080,
        forceTLS: (config.scheme === 'https' || window.location.protocol === 'https:'),
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        },
    });

    const connection = echo.connector?.pusher?.connection;

    if (connection) {
        const updateState = (state) => {
            if (state === 'connected') {
                setStatus('En vivo', 'text-success');
            } else if (state === 'connecting') {
                setStatus('Conectando...', 'text-secondary');
            } else if (state === 'unavailable' || state === 'failed') {
                setStatus('Sin conexión', 'text-danger');
            } else if (state === 'disconnected') {
                setStatus('Sin conexión', 'text-secondary');
            }
        };

        if (connection.state) {
            updateState(connection.state);
        }

        connection.bind('state_change', (states) => {
            updateState(states.current);
        });

        connection.bind('connected', () => setStatus('En vivo', 'text-success'));
        connection.bind('disconnected', () => setStatus('Sin conexión', 'text-secondary'));
        connection.bind('unavailable', () => setStatus('Sin conexión', 'text-danger'));
        connection.bind('failed', () => setStatus('Sin conexión', 'text-danger'));
    }

    try {
        echo.private('inventory').listen('.inventory.changed', (event) => {
            showToast(event);
            scheduleRefresh();
        });
    } catch (err) {
        console.warn('Error al suscribirse al canal inventory:', err);
    }
})();
