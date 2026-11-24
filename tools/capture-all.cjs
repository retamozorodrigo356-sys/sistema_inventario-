/**
 * tools/capture-all.cjs
 * Captura las pantallas del sistema usando Puppeteer Core
 */

const puppeteer = require('puppeteer-core');
const fs = require('fs');
const path = require('path');

const CHROME = process.env.CHROME_PATH || 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const BASE = process.env.APP_URL || 'http://127.0.0.1:8000';
const OUT = path.join(__dirname, '..', 'docs', 'capturas');

fs.mkdirSync(OUT, { recursive: true });

const ADMIN_SHOTS = [
    { url: '/login', file: '01-login.png', title: 'Login' },
    { url: '/dashboard', file: '02-dashboard.png', title: 'Dashboard' },
    { url: '/productos', file: '03-productos-listado.png', title: 'Listado Productos' },
    { url: '/productos/crear', file: '04-productos-registrar.png', title: 'Registrar Producto' },
    { url: '/productos/1/editar', file: '05-productos-editar.png', title: 'Editar Producto' },
    { url: '/categorias', file: '06-categorias.png', title: 'Categorías' },
    { url: '/categorias/crear', file: '07-categorias-registrar.png', title: 'Registrar Categoría' },
    { url: '/categorias/1/editar', file: '08-categorias-editar.png', title: 'Editar Categoría' },
    { url: '/entradas', file: '09-entradas.png', title: 'Entradas' },
    { url: '/entradas/crear', file: '10-entradas-registrar.png', title: 'Registrar Entrada' },
    { url: '/salidas', file: '11-salidas.png', title: 'Salidas' },
    { url: '/salidas/crear', file: '12-salidas-registrar.png', title: 'Registrar Salida' },
    { url: '/movimientos', file: '13-movimientos.png', title: 'Movimientos' },
    { url: '/reportes', file: '14-reportes.png', title: 'Reportes' },
    { url: '/usuarios', file: '15-usuarios.png', title: 'Usuarios' },
    { url: '/usuarios/crear', file: '16-usuarios-registrar.png', title: 'Registrar Usuario' },
    { url: '/usuarios/1/editar', file: '17-usuarios-editar.png', title: 'Editar Usuario' },
];

const CONSULTOR_SHOTS = [
    { url: '/dashboard', file: '18-dashboard-consultor.png', title: 'Dashboard Consultor' },
    { url: '/productos', file: '19-productos-consultor-solo-lectura.png', title: 'Productos Consultor' },
    { url: '/movimientos', file: '20-movimientos-consultor.png', title: 'Movimientos Consultor' },
];

async function main() {
    console.log('🚀 Iniciando Puppeteer para capturas...');
    const browser = await puppeteer.launch({
        executablePath: CHROME,
        headless: 'new',
        defaultViewport: { width: 1440, height: 1000 },
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--window-size=1440,1000'
        ]
    });

    const page = await browser.newPage();
    page.setDefaultTimeout(20000);

    // 1. Capturar pantalla de Login antes de autenticarse
    console.log('📸 Capturando 01-login.png...');
    await page.goto(`${BASE}/login`, { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(OUT, '01-login.png'), fullPage: true });

    // 2. Login como Admin
    console.log('🔑 Iniciando sesión como admin (admin@inventario.test)...');
    await page.type('input[name="login"]', 'admin@inventario.test');
    await page.type('input[name="password"]', 'admin123');
    await Promise.all([
        page.click('button[type="submit"]'),
        page.waitForNavigation({ waitUntil: 'networkidle2' })
    ]);

    const currentUrl = page.url();
    console.log(`📍 URL después de login: ${currentUrl}`);

    if (currentUrl.includes('correo/verificar')) {
        throw new Error('❌ Error: El usuario admin fue redirigido a verificación de correo.');
    }

    // Capturar vistas de Administrador
    for (let i = 1; i < ADMIN_SHOTS.length; i++) {
        const shot = ADMIN_SHOTS[i];
        console.log(`📸 Capturando [${shot.file}] ${shot.title} en ${shot.url}...`);
        await page.goto(`${BASE}${shot.url}`, { waitUntil: 'networkidle2' });
        const pageUrl = page.url();
        console.log(`   -> URL cargada: ${pageUrl}`);
        if (pageUrl.includes('login') || pageUrl.includes('correo/verificar')) {
            console.warn(`   ⚠️ Alerta: Redirigido a ${pageUrl}`);
        }
        await page.screenshot({ path: path.join(OUT, shot.file), fullPage: true });
    }

    // 3. Logout y Login como Consultor
    console.log('🔑 Cerrando sesión de admin...');
    await page.goto(`${BASE}/dashboard`, { waitUntil: 'networkidle2' });
    // Logout enviando form logout o navegando
    await page.evaluate(() => {
        const form = document.querySelector('form[action*="logout"]');
        if (form) form.submit();
    });
    await new Promise(r => setTimeout(r, 1500));

    console.log('🔑 Iniciando sesión como consultor (consultor@inventario.test)...');
    await page.goto(`${BASE}/login`, { waitUntil: 'networkidle2' });
    await page.type('input[name="login"]', 'consultor@inventario.test');
    await page.type('input[name="password"]', 'consultor123');
    await Promise.all([
        page.click('button[type="submit"]'),
        page.waitForNavigation({ waitUntil: 'networkidle2' })
    ]);

    console.log(`📍 URL consultor: ${page.url()}`);

    for (const shot of CONSULTOR_SHOTS) {
        console.log(`📸 Capturando [${shot.file}] ${shot.title} en ${shot.url}...`);
        await page.goto(`${BASE}${shot.url}`, { waitUntil: 'networkidle2' });
        console.log(`   -> URL cargada: ${page.url()}`);
        await page.screenshot({ path: path.join(OUT, shot.file), fullPage: true });
    }

    await browser.close();
    console.log('🎉 ¡Todas las capturas se completaron correctamente!');
}

main().catch(err => {
    console.error('Error fatal:', err);
    process.exit(1);
});
