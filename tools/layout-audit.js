/**
 * Auditoría de maquetación con Chrome headless (CDP).
 *
 * Uso:  node tools/layout-audit.js
 *
 * Inicia Chrome sin interfaz, inicia sesión como admin y comprueba en cada
 * página del sistema que:
 *   - la ventana no tenga desbordamiento horizontal,
 *   - los encabezados de tabla no se partan en dos líneas,
 *   - ninguna celda oculte contenido,
 *   - las tarjetas no se salgan del viewport.
 */

import { spawn } from 'node:child_process';
import os from 'node:os';
import path from 'node:path';
import fs from 'node:fs';

const CHROME = process.env.CHROME_PATH || 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const PORT = Number(process.env.AUDIT_PORT || 9333);
const BASE = process.env.APP_URL || 'http://127.0.0.1:8000';

const PAGES = [
    '/dashboard',
    '/productos',
    '/productos/crear',
    '/categorias',
    '/categorias/crear',
    '/entradas',
    '/entradas/crear',
    '/salidas',
    '/salidas/crear',
    '/movimientos',
    '/reportes',
    '/usuarios',
    '/usuarios/crear',
];

const VIEWPORTS = [
    { name: 'escritorio 1440', width: 1440, height: 1600, mobile: false },
    { name: 'portátil 1024', width: 1024, height: 900, mobile: false },
    { name: 'móvil 390', width: 390, height: 844, mobile: true },
];

const LOGIN = `(() => {
    const form = document.querySelector('form');
    if (!form) return 'sin formulario';
    const data = new URLSearchParams(new FormData(form));
    data.set('login', 'admin');
    data.set('password', 'admin123');
    return fetch(form.action, { method: 'POST', body: data, redirect: 'manual' })
        .then((res) => 'login:' + res.status);
})()`;

const AUDIT = `(() => {
    const problems = [];
    const doc = document.documentElement;
    const wide = window.innerWidth >= 992;

    if (doc.scrollWidth > window.innerWidth + 2) {
        problems.push('ventana con scroll horizontal: ' + doc.scrollWidth + ' > ' + window.innerWidth);
    }

    const sidebar = document.getElementById('sidebar');
    if (!wide && sidebar) {
        const rect = sidebar.getBoundingClientRect();
        if (rect.right > 0) {
            problems.push('sidebar visible en móvil (right=' + Math.round(rect.right) + ')');
        }
    }

    document.querySelectorAll('table').forEach((table, index) => {
        if (table.tHead) {
            [...table.tHead.rows[0].cells].forEach((cell) => {
                const rect = cell.getBoundingClientRect();
                if (rect.height > 44) {
                    problems.push('tabla #' + index + ': encabezado "' + cell.textContent.trim().slice(0, 32) + '" partido (' + Math.round(rect.height) + 'px)');
                }
            });
        }

        table.querySelectorAll('tbody td').forEach((cell) => {
            if (cell.classList.contains('cell-truncate') || cell.hasAttribute('colspan')) return;
            if (cell.scrollWidth > cell.clientWidth + 2) {
                problems.push('tabla #' + index + ': contenido recortado "' + cell.textContent.trim().slice(0, 32) + '"');
            }
        });

        const wrapper = table.closest('.table-responsive');
        if (wrapper && wide && wrapper.scrollWidth > wrapper.clientWidth + 2) {
            problems.push('tabla #' + index + ': scroll horizontal en pantalla ancha (' + wrapper.scrollWidth + 'px)');
        }
    });

    document.querySelectorAll('.card, .filter-bar').forEach((box, index) => {
        const rect = box.getBoundingClientRect();
        if (rect.right > window.innerWidth + 2 || rect.left < -2) {
            problems.push('bloque #' + index + ' fuera del viewport (left=' + Math.round(rect.left) + ', right=' + Math.round(rect.right) + ')');
        }
    });

    return problems;
})()`;

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

async function newPage() {
    try {
        const res = await fetch(`http://127.0.0.1:${PORT}/json/new?about:blank`, { method: 'PUT' });
        if (res.ok) return res.json();
    } catch (_) {
        // probamos con GET (Chrome antiguos)
    }
    const res = await fetch(`http://127.0.0.1:${PORT}/json/new?about:blank`);
    return res.json();
}

async function main() {
    const profile = fs.mkdtempSync(path.join(os.tmpdir(), 'audit-'));
    const chrome = spawn(CHROME, [
        '--headless=new',
        `--remote-debugging-port=${PORT}`,
        `--user-data-dir=${profile}`,
        '--window-size=1440,2200',
        '--no-first-run',
        '--disable-gpu',
        'about:blank',
    ], { stdio: 'ignore' });

    try {
        let info = null;
        for (let i = 0; i < 40 && !info; i++) {
            await sleep(250);
            try {
                const res = await fetch(`http://127.0.0.1:${PORT}/json/version`);
                if (res.ok) info = await res.json();
            } catch (_) {
                // Chrome aún no responde
            }
        }
        if (!info) throw new Error('No se pudo conectar a Chrome');

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
            const result = await send('Runtime.evaluate', {
                expression,
                returnByValue: true,
                awaitPromise: true,
            });
            if (result.exceptionDetails) throw new Error(result.exceptionDetails.exception?.description || result.exceptionDetails.text);
            return result.result.value;
        };

        await send('Page.enable');
        await send('Runtime.enable');

        await send('Page.navigate', { url: `${BASE}/login` });
        await sleep(1500);
        console.log('Sesión:', await evaluate(LOGIN));
        await sleep(600);

        let failures = 0;

        for (const viewport of VIEWPORTS) {
            await send('Emulation.setDeviceMetricsOverride', {
                width: viewport.width,
                height: viewport.height,
                deviceScaleFactor: 1,
                mobile: viewport.mobile,
            });

            console.log(`\n=== ${viewport.name} ===`);

            for (const page of PAGES) {
                await send('Page.navigate', { url: BASE + page });
                await sleep(1500);

                const problems = (await evaluate(AUDIT)) || [];

                if (problems.length === 0) {
                    const tables = await evaluate('document.querySelectorAll("table").length');
                    console.log(`OK     ${page} (${tables} tablas)`);
                } else {
                    failures += problems.length;
                    console.log(`FALLA  ${page}`);
                    problems.forEach((problem) => console.log('   - ' + problem));
                }
            }
        }

        console.log(failures === 0 ? '\nMaquetación correcta en todas las páginas.' : `\n${failures} problema(s) de maquetación.`);
        process.exitCode = failures === 0 ? 0 : 1;
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
