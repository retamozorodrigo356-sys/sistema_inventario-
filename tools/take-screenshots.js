/**
 * Genera capturas de pantalla de cada módulo para el informe.
 *
 * Uso:  node tools/take-screenshots.js
 *
 * Requiere el servidor corriendo (php artisan serve) y guarda los archivos
 * PNG en docs/capturas/.
 */

import { spawn } from 'node:child_process';
import os from 'node:os';
import path from 'node:path';
import fs from 'node:fs';

const CHROME = process.env.CHROME_PATH || 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const PORT = Number(process.env.SHOT_PORT || 9334);
const BASE = process.env.APP_URL || 'http://127.0.0.1:8000';
const OUT = path.join(process.cwd(), 'docs', 'capturas');
const VIEWPORT = { width: 1440, height: 1000 };

const ADMIN_SHOTS = [
    { url: '/login', file: '01-login.png' },
    { url: '/dashboard', file: '02-dashboard.png' },
    { url: '/productos', file: '03-productos-listado.png' },
    { url: '/productos/crear', file: '04-productos-registrar.png' },
    { url: '/productos/1/editar', file: '05-productos-editar.png' },
    { url: '/categorias', file: '06-categorias.png' },
    { url: '/categorias/crear', file: '07-categorias-registrar.png' },
    { url: '/categorias/1/editar', file: '08-categorias-editar.png' },
    { url: '/entradas', file: '09-entradas.png' },
    { url: '/entradas/crear', file: '10-entradas-registrar.png' },
    { url: '/salidas', file: '11-salidas.png' },
    { url: '/salidas/crear', file: '12-salidas-registrar.png' },
    { url: '/movimientos', file: '13-movimientos.png' },
    { url: '/reportes', file: '14-reportes.png' },
    { url: '/usuarios', file: '15-usuarios.png' },
    { url: '/usuarios/crear', file: '16-usuarios-registrar.png' },
    { url: '/usuarios/1/editar', file: '17-usuarios-editar.png' },
];

const CONSULTOR_SHOTS = [
    { url: '/dashboard', file: '18-dashboard-consultor.png' },
    { url: '/productos', file: '19-productos-consultor-solo-lectura.png' },
    { url: '/movimientos', file: '20-movimientos-consultor.png' },
];

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

async function newPage() {
    try {
        const res = await fetch(`http://127.0.0.1:${PORT}/json/new?about:blank`, { method: 'PUT' });
        if (res.ok) return res.json();
    } catch (_) {
        // Chrome antiguo: método GET
    }
    const res = await fetch(`http://127.0.0.1:${PORT}/json/new?about:blank`);
    return res.json();
}

async function main() {
    fs.mkdirSync(OUT, { recursive: true });

    const profile = fs.mkdtempSync(path.join(os.tmpdir(), 'shots-'));
    const chrome = spawn(CHROME, [
        '--headless=new',
        `--remote-debugging-port=${PORT}`,
        `--user-data-dir=${profile}`,
        `--window-size=${VIEWPORT.width},${VIEWPORT.height}`,
        '--no-first-run',
        '--disable-gpu',
        'about:blank',
    ], { stdio: 'ignore' });

    try {
        let version = null;
        for (let i = 0; i < 40 && !version; i++) {
            await sleep(250);
            try {
                const res = await fetch(`http://127.0.0.1:${PORT}/json/version`);
                if (res.ok) version = await res.json();
            } catch (_) {
                // Chrome aún no responde
            }
        }
        if (!version) throw new Error('No se pudo conectar a Chrome');

        const page = await newPage();
        const socket = new WebSocket(page.webSocketDebuggerUrl);
        await new Promise((resolve, reject) => {
            socket.onopen = resolve;
            socket.onerror = () => reject(new Error('WebSocket falló'));
        });

        let id = 0;
        const pending = new Map();
        socket.onmessage = (event) => {
            const message = JSON.parse(event.data);
            if (message.id && pending.has(message.id)) {
                const { resolve, reject } = pending.get(message.id);
                pending.delete(message.id);
                message.error ? reject(new Error(message.error.message)) : resolve(message.result);
            }
        };

        const send = (method, params = {}) => new Promise((resolve, reject) => {
            const msgId = ++id;
            pending.set(msgId, { resolve, reject });
            socket.send(JSON.stringify({ id: msgId, method, params }));
        });

        const evaluate = async (expression) => {
            const result = await send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true });
            if (result.exceptionDetails) throw new Error(result.exceptionDetails.exception?.description || result.exceptionDetails.text);
            return result.result.value;
        };

        const goTo = async (url) => {
            await send('Page.navigate', { url: BASE + url });
            await sleep(1400);
        };

        const login = async (user, password) => {
            await goTo('/login');
            const status = await evaluate(`(() => {
                const form = document.querySelector('form');
                if (!form) return 'sin formulario';
                const data = new URLSearchParams(new FormData(form));
                data.set('login', '${user}');
                data.set('password', '${password}');
                return fetch(form.action, { method: 'POST', body: data, redirect: 'manual' })
                    .then((res) => 'login:' + res.status);
            })()`);
            await sleep(800);
            return status;
        };

        const logout = async () => {
            await evaluate(`(() => {
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!token) return 'sin token';
                return fetch('/logout', { method: 'POST', body: '_token=' + encodeURIComponent(token), headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, redirect: 'manual' })
                    .then((res) => 'logout:' + res.status);
            })()`);
            await sleep(600);
        };

        const shoot = async (shot) => {
            await goTo(shot.url);

            const size = await evaluate(`(() => ({
                width: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth),
                height: Math.max(document.documentElement.scrollHeight, document.body.scrollHeight),
            }))()`);

            const { data } = await send('Page.captureScreenshot', {
                format: 'png',
                captureBeyondViewport: true,
                clip: { x: 0, y: 0, width: size.width, height: size.height, scale: 1 },
            });

            fs.writeFileSync(path.join(OUT, shot.file), Buffer.from(data, 'base64'));
            const kb = Math.round(Buffer.byteLength(data, 'base64') / 1024);
            console.log(`OK  ${shot.file}  (${size.width}x${size.height}, ${kb} KB)`);
        };

        await send('Page.enable');
        await send('Emulation.setDeviceMetricsOverride', {
            width: VIEWPORT.width,
            height: VIEWPORT.height,
            deviceScaleFactor: 1,
            mobile: false,
        });

        // Login como admin (la primera captura es del formulario de acceso)
        await goTo('/login');
        await shoot(ADMIN_SHOTS[0]);
        console.log(await login('admin', 'admin123'));

        for (const shot of ADMIN_SHOTS.slice(1)) {
            await shoot(shot);
        }

        // Vista del rol Consultor (menú reducido y tablas en solo lectura)
        await logout();
        console.log(await login('consultor', 'consultor123'));

        for (const shot of CONSULTOR_SHOTS) {
            await shoot(shot);
        }

        console.log(`\n${ADMIN_SHOTS.length + CONSULTOR_SHOTS.length} capturas guardadas en ${OUT}`);
    } finally {
        try {
            chrome.kill();
        } catch (_) {
            // nada
        }
    }
}

main().catch((error) => {
    console.error('Error:', error.message);
    process.exit(1);
});
