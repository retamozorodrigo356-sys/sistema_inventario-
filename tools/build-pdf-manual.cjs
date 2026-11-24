/**
 * build-pdf-manual.js
 * Genera MANUAL_DE_USUARIO.pdf con todas las capturas del sistema.
 * Uso: node tools/build-pdf-manual.js
 */

const puppeteer = require('puppeteer-core');
const fs = require('fs');
const path = require('path');

const CHROME = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const CAPTURAS_DIR = path.join(__dirname, '..', 'docs', 'capturas');
const LOGOS_DIR = path.join(__dirname, '..', 'docs', 'logos');
const OUTPUT = path.join(__dirname, '..', 'MANUAL_DE_USUARIO.pdf');

// Lee imagen como base64
function imgBase64(filename, baseDir = CAPTURAS_DIR) {
  const fp = path.join(baseDir, filename);
  if (!fs.existsSync(fp)) return '';
  const buf = fs.readFileSync(fp);
  return 'data:image/png;base64,' + buf.toString('base64');
}

const logoInstituto = imgBase64('logo-instituto.png', LOGOS_DIR);
const logoCarrera = imgBase64('logo-carrera.png', LOGOS_DIR);

const capturas = {
  login: imgBase64('01-login.png'),
  dashboard: imgBase64('02-dashboard.png'),
  productosListado: imgBase64('03-productos-listado.png'),
  productosRegistrar: imgBase64('04-productos-registrar.png'),
  productosEditar: imgBase64('05-productos-editar.png'),
  categorias: imgBase64('06-categorias.png'),
  categoriasRegistrar: imgBase64('07-categorias-registrar.png'),
  categoriasEditar: imgBase64('08-categorias-editar.png'),
  entradas: imgBase64('09-entradas.png'),
  entradasRegistrar: imgBase64('10-entradas-registrar.png'),
  salidas: imgBase64('11-salidas.png'),
  salidasRegistrar: imgBase64('12-salidas-registrar.png'),
  movimientos: imgBase64('13-movimientos.png'),
  reportes: imgBase64('14-reportes.png'),
  usuarios: imgBase64('15-usuarios.png'),
  usuariosRegistrar: imgBase64('16-usuarios-registrar.png'),
  usuariosEditar: imgBase64('17-usuarios-editar.png'),
  dashboardConsultor: imgBase64('18-dashboard-consultor.png'),
  productosConsultor: imgBase64('19-productos-consultor-solo-lectura.png'),
  movimientosConsultor: imgBase64('20-movimientos-consultor.png'),
};

function sec(titulo, nivel = 'h2') {
  return `<${nivel} class="section-title">${titulo}</${nivel}>`;
}

function captura(src, caption) {
  if (!src) return '';
  return `
    <figure>
      <img src="${src}" alt="${caption}" />
      <figcaption>${caption}</figcaption>
    </figure>`;
}

const html = `<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"/>
<title>Manual de Usuario - Sistema de Inventario Web</title>
<style>
  @page { size: A4; margin: 20mm 18mm 20mm 18mm; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11pt; color: #1a1a2e; line-height: 1.55; }

  /* ---- PORTADA / CARÁTULA INSTITUCIONAL ---- */
  .cover {
    display: flex; flex-direction: column; justify-content: space-between;
    min-height: 92vh; padding: 24px 20px;
    border: 3px double #0f3460; border-radius: 8px;
    background: #ffffff;
    page-break-after: always;
    box-sizing: border-box;
  }
  .cover-header {
    display: flex; align-items: center; justify-content: space-between;
    padding-bottom: 14px; border-bottom: 2px solid #0f3460;
  }
  .cover-logo-img {
    height: 105px; width: 105px; object-fit: contain; flex-shrink: 0;
  }
  .cover-inst-text {
    flex: 1; text-align: center; padding: 0 12px;
  }
  .cover-inst-text h3 {
    font-size: 10pt; font-weight: 800; color: #0f3460; text-transform: uppercase; margin: 0; line-height: 1.25;
  }
  .cover-inst-text h2 {
    font-size: 13.5pt; font-weight: 900; color: #b7094c; text-transform: uppercase; margin: 3px 0 2px 0; letter-spacing: 0.5px;
  }
  .cover-inst-text .cover-city {
    font-size: 8.5pt; font-weight: 700; color: #64748b; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;
  }
  .cover-inst-text .cover-carrera-lbl {
    font-size: 8.5pt; font-weight: 700; color: #0f3460; text-transform: uppercase; margin: 6px 0 2px 0;
  }
  .cover-inst-text .cover-carrera-val {
    font-size: 9.5pt; font-weight: 800; color: #0284c7; text-transform: uppercase; margin: 0; line-height: 1.25;
  }

  .cover-body {
    text-align: center; padding: 24px 10px; flex-grow: 1; display: flex; flex-direction: column; justify-content: center; align-items: center;
  }
  .cover-doc-type {
    display: inline-block; padding: 5px 18px; background: #e0f2fe; color: #0369a1; font-size: 9pt; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: 1.5px; border: 1px solid #7dd3fc; margin-bottom: 16px;
  }
  .cover-title {
    font-size: 26pt; font-weight: 900; color: #0f172a; margin: 0 0 8px 0; letter-spacing: 0.5px; line-height: 1.2;
  }
  .cover-subtitle {
    font-size: 16pt; font-weight: 800; color: #0f3460; margin: 0 0 14px 0; text-transform: uppercase;
  }
  .cover-desc {
    font-size: 10pt; color: #475569; max-width: 580px; margin: 0 auto; line-height: 1.5; font-style: italic;
  }

  .cover-meta {
    margin: 22px auto 0 auto; width: 100%; max-width: 520px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px 18px; background: #f8fafc; font-size: 9pt; color: #1e293b;
  }
  .cover-meta table { width: 100%; border: none; margin: 0; }
  .cover-meta td { border: none; padding: 4px 6px; }

  .cover-footer {
    text-align: center; padding-top: 12px; border-top: 2px solid #0f3460; margin-top: 15px;
  }
  .cover-footer .city {
    font-size: 9.5pt; font-weight: 700; color: #0f3460; letter-spacing: 1px; margin: 0;
  }
  .cover-footer .year {
    font-size: 9pt; font-weight: 600; color: #64748b; margin-top: 2px;
  }

  /* ---- ÍNDICE ---- */
  .toc { page-break-after: always; padding-top: 20px; }
  .toc h1 { font-size: 20pt; color: #0f3460; border-bottom: 3px solid #e94560; padding-bottom: 8px; margin-bottom: 20px; }
  .toc ul { list-style: none; }
  .toc > ul > li { margin-bottom: 6px; }
  .toc > ul > li > ul { padding-left: 20px; margin-top: 4px; }
  .toc a { text-decoration: none; color: #16213e; font-size: 10.5pt; font-weight: 500; }
  .toc a:hover { color: #e94560; }
  .toc-num { display: inline-block; width: 28px; font-weight: 600; color: #0f3460; }

  /* ---- SECCIONES ---- */
  .chapter { page-break-before: always; padding-top: 10px; }
  h1.section-title { font-size: 20pt; color: #0f3460; border-bottom: 3px solid #e94560; padding-bottom: 8px; margin-bottom: 20px; page-break-after: avoid; }
  h2.section-title { font-size: 14pt; color: #16213e; border-left: 4px solid #e94560; padding-left: 10px; margin: 22px 0 12px 0; page-break-after: avoid; }
  h3.section-title { font-size: 12pt; color: #0f3460; margin: 18px 0 8px 0; page-break-after: avoid; }

  p { margin-bottom: 10px; text-align: justify; }
  ul, ol { margin: 8px 0 12px 24px; }
  li { margin-bottom: 5px; }

  /* ---- IMÁGENES ---- */
  figure { margin: 18px 0 22px 0; text-align: center; page-break-inside: avoid; }
  figure img { max-width: 100%; max-height: 480px; object-fit: contain; border: 1px solid #dee2e6; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,.10); }
  figcaption { font-size: 9pt; color: #555; margin-top: 7px; font-style: italic; }

  /* ---- TABLAS ---- */
  table { width: 100%; border-collapse: collapse; margin: 12px 0 20px 0; font-size: 10pt; page-break-inside: avoid; }
  th { background: #0f3460; color: #fff; padding: 9px 12px; text-align: left; font-weight: 600; }
  td { padding: 8px 12px; border-bottom: 1px solid #dee2e6; vertical-align: middle; }
  tr:nth-child(even) td { background: #f8f9fa; }

  /* ---- ALERTAS / NOTAS ---- */
  .note { background: #e8f4fd; border-left: 4px solid #2196f3; padding: 11px 16px; border-radius: 0 6px 6px 0; margin: 14px 0; font-size: 10pt; page-break-inside: avoid; }
  .warning { background: #fff3e0; border-left: 4px solid #ff9800; padding: 11px 16px; border-radius: 0 6px 6px 0; margin: 14px 0; font-size: 10pt; page-break-inside: avoid; }
  .success { background: #e8f5e9; border-left: 4px solid #4caf50; padding: 11px 16px; border-radius: 0 6px 6px 0; margin: 14px 0; font-size: 10pt; page-break-inside: avoid; }
  .tag-note { display: inline-block; background: #2196f3; color: #fff; font-size: 8pt; font-weight: 700; padding: 2px 7px; border-radius: 3px; margin-right: 6px; }
  .tag-warn { display: inline-block; background: #ff9800; color: #fff; font-size: 8pt; font-weight: 700; padding: 2px 7px; border-radius: 3px; margin-right: 6px; }
  .tag-succ { display: inline-block; background: #4caf50; color: #fff; font-size: 8pt; font-weight: 700; padding: 2px 7px; border-radius: 3px; margin-right: 6px; }

  /* ---- BADGE ROLES Y PERMISOS ---- */
  .badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 8.5pt; font-weight: 600; }
  .badge-admin { background: #e94560; color: #fff; }
  .badge-almacen { background: #0f3460; color: #fff; }
  .badge-consultor { background: #16213e; color: #a8dadc; }
  .perm-yes { background-color: #e8f5e9; color: #2e7d32; font-weight: 700; padding: 3px 10px; border-radius: 4px; display: inline-block; font-size: 8.5pt; }
  .perm-no { background-color: #ffebee; color: #c62828; font-weight: 700; padding: 3px 10px; border-radius: 4px; display: inline-block; font-size: 8.5pt; }

  /* ---- SWAGGER UI STYLING ---- */
  .swagger-ui-card {
    border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden; margin: 18px 0 24px 0; box-shadow: 0 4px 12px rgba(0,0,0,0.08); background: #ffffff; page-break-inside: avoid;
  }
  .swagger-header {
    background: #1b1b1b; color: #ffffff; padding: 10px 16px; display: flex; align-items: center; justify-content: space-between;
  }
  .swagger-brand {
    display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 13pt; color: #85ea2d;
  }
  .swagger-brand span.sub {
    font-size: 8pt; color: #9ca3af; font-weight: 400; margin-left: 6px;
  }
  .swagger-badge-oas {
    background: #49cc90; color: #fff; font-size: 7.5pt; font-weight: 700; padding: 2px 8px; border-radius: 10px;
  }
  .swagger-subbar {
    background: #f3f4f6; padding: 10px 16px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;
  }
  .swagger-server-info {
    font-size: 8.5pt; color: #374151;
  }
  .swagger-server-info code {
    background: #e5e7eb; padding: 2px 6px; border-radius: 4px; font-family: monospace; color: #111827;
  }
  .swagger-btn-auth {
    background: #ffffff; color: #49cc90; border: 2px solid #49cc90; padding: 3px 12px; border-radius: 4px; font-size: 8.5pt; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;
  }
  .swagger-endpoint-list {
    padding: 10px 16px 14px 16px;
  }
  .swagger-tag-title {
    font-size: 11pt; font-weight: 700; color: #1f2937; margin: 12px 0 6px 0; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px;
  }
  .swagger-endpoint-row {
    display: flex; align-items: center; border-radius: 4px; margin-bottom: 6px; padding: 5px 10px; font-size: 8.5pt;
  }
  .op-get { background: #ebf3fb; border: 1px solid #b6d4fe; }
  .op-get .method { background: #0d6efd; color: #fff; font-weight: 800; padding: 2px 8px; border-radius: 3px; font-size: 7.5pt; width: 52px; text-align: center; }
  .op-post { background: #eaf7ee; border: 1px solid #badbcc; }
  .op-post .method { background: #198754; color: #fff; font-weight: 800; padding: 2px 8px; border-radius: 3px; font-size: 7.5pt; width: 52px; text-align: center; }
  .op-put { background: #fff8e7; border: 1px solid #ffe69c; }
  .op-put .method { background: #d97706; color: #fff; font-weight: 800; padding: 2px 8px; border-radius: 3px; font-size: 7.5pt; width: 52px; text-align: center; }
  .op-delete { background: #fdf2f2; border: 1px solid #f5c2c7; }
  .op-delete .method { background: #dc3545; color: #fff; font-weight: 800; padding: 2px 8px; border-radius: 3px; font-size: 7.5pt; width: 52px; text-align: center; }
  .swagger-endpoint-row .path { font-family: monospace; font-size: 9pt; font-weight: 700; color: #1f2937; margin: 0 10px; }
  .swagger-endpoint-row .desc { color: #4b5563; font-size: 8pt; margin-left: auto; }
  .code-json { background: #1e1e1e; color: #d4d4d4; font-family: 'Consolas', 'Courier New', monospace; font-size: 8pt; padding: 10px 14px; border-radius: 6px; overflow-x: auto; margin: 8px 0; line-height: 1.4; }

  /* ---- PIE DE PÁGINA ---- */
  @page { @bottom-center { content: "Sistema de Inventario Web  |  Pág. " counter(page); font-size: 9pt; color: #888; } }
</style>
</head>
<body>

<!-- ==================== PORTADA / CARÁTULA INSTITUCIONAL ==================== -->
<div class="cover">
  <div class="cover-header">
    <img src="${logoInstituto}" class="cover-logo-img" alt="Logo Instituto" />
    <div class="cover-inst-text">
      <h3>Instituto de Educación Superior Tecnológico Público</h3>
      <h2>"César A. Guardia Mayorga"</h2>
      <p class="cover-city">Coracora — Parinacochas — Ayacucho</p>
      <p class="cover-carrera-lbl">Programa de Estudios:</p>
      <p class="cover-carrera-val">Arquitectura de Plataformas y Servicios de Tecnologías de Información</p>
    </div>
    <img src="${logoCarrera}" class="cover-logo-img" alt="Logo Carrera" />
  </div>

  <div class="cover-body">
    <div class="cover-doc-type">Documento Técnico e Instructivo</div>
    <h1 class="cover-title">MANUAL DE USUARIO</h1>
    <h2 class="cover-subtitle">Sistema de Inventario Web</h2>
    <p class="cover-desc">
      Guía integral de operación técnica y funcional para la administración centralizada de inventarios, control de stock en tiempo real, registro de entradas, despacho de salidas, trazabilidad de movimientos y reportes institucionales.
    </p>

    <div class="cover-meta">
      <table>
        <tr>
          <td style="font-weight: 700; color: #0f3460; width: 40%;">Módulo Funcional:</td>
          <td>Control de Almacén y Gestión de Bienes</td>
        </tr>
        <tr>
          <td style="font-weight: 700; color: #0f3460;">Plataforma Tecnológica:</td>
          <td>Laravel 12 · PHP 8.5 · MariaDB / MySQL · Bootstrap 5</td>
        </tr>
        <tr>
          <td style="font-weight: 700; color: #0f3460;">Versión del Software:</td>
          <td>Versión 1.0 (Producción)</td>
        </tr>
        <tr>
          <td style="font-weight: 700; color: #0f3460;">Perfiles de Acceso:</td>
          <td>Administrador · Encargado de Almacén · Consultor</td>
        </tr>
      </table>
    </div>
  </div>

  <div class="cover-footer">
    <p class="city">CORACORA – PARINACOCHAS – AYACUCHO</p>
    <p class="year">Noviembre 2026</p>
  </div>
</div>

<!-- ==================== ÍNDICE ==================== -->
<div class="toc">
  <h1>Tabla de Contenidos</h1>
  <ul>
    <li><span class="toc-num">1.</span><a href="#intro">Introducción</a></li>
    <li><span class="toc-num">2.</span><a href="#acceso">Acceso al Sistema</a></li>
    <li><span class="toc-num">3.</span><a href="#dashboard">Panel Principal (Dashboard)</a></li>
    <li><span class="toc-num">4.</span><a href="#productos">Gestión de Productos</a>
      <ul>
        <li><span class="toc-num">4.1</span><a href="#productos-listado">Listado de Productos</a></li>
        <li><span class="toc-num">4.2</span><a href="#productos-registrar">Registrar Producto</a></li>
        <li><span class="toc-num">4.3</span><a href="#productos-editar">Editar / Eliminar Producto</a></li>
      </ul>
    </li>
    <li><span class="toc-num">5.</span><a href="#categorias">Gestión de Categorías</a>
      <ul>
        <li><span class="toc-num">5.1</span><a href="#categorias-listado">Listado</a></li>
        <li><span class="toc-num">5.2</span><a href="#categorias-registrar">Registrar Categoría</a></li>
        <li><span class="toc-num">5.3</span><a href="#categorias-editar">Editar Categoría</a></li>
      </ul>
    </li>
    <li><span class="toc-num">6.</span><a href="#entradas">Registro de Entradas</a>
      <ul>
        <li><span class="toc-num">6.1</span><a href="#entradas-listado">Listado de Entradas</a></li>
        <li><span class="toc-num">6.2</span><a href="#entradas-registrar">Nueva Entrada</a></li>
      </ul>
    </li>
    <li><span class="toc-num">7.</span><a href="#salidas">Registro de Salidas</a>
      <ul>
        <li><span class="toc-num">7.1</span><a href="#salidas-listado">Listado de Salidas</a></li>
        <li><span class="toc-num">7.2</span><a href="#salidas-registrar">Nueva Salida</a></li>
      </ul>
    </li>
    <li><span class="toc-num">8.</span><a href="#movimientos">Historial de Movimientos</a></li>
    <li><span class="toc-num">9.</span><a href="#reportes">Reportes</a></li>
    <li><span class="toc-num">10.</span><a href="#usuarios">Gestión de Usuarios</a>
      <ul>
        <li><span class="toc-num">10.1</span><a href="#usuarios-listado">Listado</a></li>
        <li><span class="toc-num">10.2</span><a href="#usuarios-registrar">Registrar Usuario</a></li>
        <li><span class="toc-num">10.3</span><a href="#usuarios-editar">Editar Usuario</a></li>
      </ul>
    </li>
    <li><span class="toc-num">11.</span><a href="#roles">Roles y Permisos</a></li>
    <li><span class="toc-num">12.</span><a href="#consultor">Perfil Consultor (Solo Lectura)</a></li>
    <li><span class="toc-num">13.</span><a href="#swagger">Documentación de API con Swagger / OpenAPI</a>
      <ul>
        <li><span class="toc-num">13.1</span><a href="#swagger-intro">¿Qué es Swagger y OpenAPI 3.0?</a></li>
        <li><span class="toc-num">13.2</span><a href="#swagger-acceso">Acceso y Autenticación con Token (Sanctum)</a></li>
        <li><span class="toc-num">13.3</span><a href="#swagger-interfaz">Estructura de la Interfaz Swagger UI</a></li>
        <li><span class="toc-num">13.4</span><a href="#swagger-endpoints">Catálogo de Endpoints RESTful</a></li>
        <li><span class="toc-num">13.5</span><a href="#swagger-testing">Pruebas en Vivo con "Try it out"</a></li>
        <li><span class="toc-num">13.6</span><a href="#swagger-codigos">Códigos de Respuesta HTTP</a></li>
      </ul>
    </li>
    <li><span class="toc-num">14.</span><a href="#faq">Preguntas Frecuentes</a></li>
  </ul>
</div>

<!-- ==================== 1. INTRODUCCIÓN ==================== -->
<div class="chapter" id="intro">
  <h1 class="section-title">1. Introducción</h1>
  <p>El <strong>Sistema de Inventario Web</strong> es una aplicación web institucional desarrollada en <strong>Laravel 12</strong> que permite administrar de forma centralizada el inventario de bienes y materiales de la institución. Proporciona control completo sobre productos, categorías, entradas, salidas y reportes, con un sistema de roles que garantiza la seguridad de los datos.</p>

  <h2 class="section-title">1.1 Objetivos del Sistema</h2>
  <ul>
    <li>Registrar y mantener actualizado el catálogo de productos e insumos.</li>
    <li>Controlar las entradas y salidas de materiales del almacén.</li>
    <li>Generar reportes en tiempo real sobre el stock disponible.</li>
    <li>Gestionar los accesos mediante roles diferenciados (Administrador, Almacén, Consultor).</li>
  </ul>

  <h2 class="section-title">1.2 Requerimientos Técnicos</h2>
  <table>
    <tr><th>Componente</th><th>Requerimiento</th></tr>
    <tr><td>Navegador</td><td>Google Chrome 110+, Firefox 110+, Edge 110+</td></tr>
    <tr><td>Conexión</td><td>Red interna o Internet (HTTPS recomendado)</td></tr>
    <tr><td>Resolución</td><td>Mínimo 1280×720 px</td></tr>
    <tr><td>JavaScript</td><td>Habilitado</td></tr>
  </table>

  <h2 class="section-title">1.3 Convenciones de este Manual</h2>
  <div class="note"><span class="tag-note">NOTA</span> Información adicional o sugerencias útiles para el usuario.</div>
  <div class="warning"><span class="tag-warn">ADVERTENCIA</span> Acciones que requieren precaución o validación previa.</div>
  <div class="success"><span class="tag-succ">CONFIRMACIÓN</span> Operación completada con éxito en el sistema.</div>
</div>

<!-- ==================== 2. ACCESO ==================== -->
<div class="chapter" id="acceso">
  <h1 class="section-title">2. Acceso al Sistema</h1>
  <p>Para ingresar al sistema navegue a la URL institucional en su navegador web. Se mostrará la pantalla de inicio de sesión donde debe ingresar sus credenciales.</p>

  ${captura(capturas.login, 'Fig. 2.1 — Pantalla de Inicio de Sesión')}

  <h2 class="section-title">2.1 Cómo Iniciar Sesión</h2>
  <ol>
    <li>Abra su navegador web e ingrese la URL del sistema.</li>
    <li>En el campo <strong>Correo Electrónico</strong>, ingrese su correo institucional o nombre de usuario.</li>
    <li>En el campo <strong>Contraseña</strong>, ingrese su contraseña secreta.</li>
    <li>Haga clic en el botón <strong>Iniciar Sesión</strong>.</li>
    <li>Si las credenciales son correctas, será redirigido al Panel Principal.</li>
  </ol>

  <div class="note"><span class="tag-note">NOTA</span> Si olvidó su contraseña, contacte al administrador del sistema o use el enlace de recuperación para restablecer sus credenciales.</div>

  <h2 class="section-title">2.2 Usuarios Predeterminados</h2>
  <table>
    <tr><th>Rol</th><th>Correo</th><th>Contraseña Inicial</th></tr>
    <tr><td><span class="badge badge-admin">Administrador</span></td><td>admin@inventario.test</td><td>admin123</td></tr>
    <tr><td><span class="badge badge-almacen">Almacén</span></td><td>almacen@inventario.test</td><td>almacen123</td></tr>
    <tr><td><span class="badge badge-consultor">Consultor</span></td><td>consultor@inventario.test</td><td>consultor123</td></tr>
  </table>
  <div class="warning"><span class="tag-warn">ADVERTENCIA</span> Cambie las contraseñas predeterminadas en el primer inicio de sesión en un entorno productivo.</div>
</div>

<!-- ==================== 3. DASHBOARD ==================== -->
<div class="chapter" id="dashboard">
  <h1 class="section-title">3. Panel Principal (Dashboard)</h1>
  <p>Después de iniciar sesión, se muestra el <strong>Panel Principal</strong>. Aquí encontrará un resumen visual del estado actual del inventario con métricas clave y accesos rápidos a los módulos del sistema.</p>

  ${captura(capturas.dashboard, 'Fig. 3.1 — Panel Principal (Dashboard) — Vista Administrador')}

  <h2 class="section-title">3.1 Tarjetas de Resumen</h2>
  <table>
    <tr><th>Tarjeta</th><th>Descripción</th></tr>
    <tr><td><strong>Total Productos</strong></td><td>Número total de productos registrados en el catálogo.</td></tr>
    <tr><td><strong>Categorías</strong></td><td>Total de familias y categorías activas en el sistema.</td></tr>
    <tr><td><strong>Entradas del Mes</strong></td><td>Total de operaciones de ingreso de materiales registradas en el mes.</td></tr>
    <tr><td><strong>Salidas del Mes</strong></td><td>Total de operaciones de egreso o despacho de materiales del mes.</td></tr>
  </table>

  <h2 class="section-title">3.2 Menú de Navegación</h2>
  <p>El menú lateral izquierdo permite acceder a todos los módulos del sistema. Los ítems disponibles varían según el rol del usuario autenticado.</p>
</div>

<!-- ==================== 4. PRODUCTOS ==================== -->
<div class="chapter" id="productos">
  <h1 class="section-title">4. Gestión de Productos</h1>
  <p>El módulo de <strong>Productos</strong> permite administrar el catálogo completo de bienes y materiales del inventario. Desde aquí puede consultar el stock actual, registrar nuevos productos y actualizar la información existente.</p>

  <h2 class="section-title" id="productos-listado">4.1 Listado de Productos</h2>
  ${captura(capturas.productosListado, 'Fig. 4.1 — Listado de Productos')}
  <p>La tabla muestra todos los productos registrados con la siguiente información:</p>
  <table>
    <tr><th>Columna</th><th>Descripción</th></tr>
    <tr><td>Código</td><td>Identificador único del producto.</td></tr>
    <tr><td>Nombre</td><td>Nombre descriptivo del producto.</td></tr>
    <tr><td>Categoría</td><td>Categoría a la que pertenece el producto.</td></tr>
    <tr><td>Stock Actual</td><td>Cantidad disponible en almacén (actualizada en tiempo real).</td></tr>
    <tr><td>Stock Mínimo</td><td>Umbral mínimo de alerta para reposición.</td></tr>
    <tr><td>Acciones</td><td>Botones para Ver, Editar y Eliminar.</td></tr>
  </table>
  <div class="note"><span class="tag-note">NOTA</span> Use el buscador en la parte superior de la tabla para filtrar productos por nombre o código.</div>

  <h2 class="section-title" id="productos-registrar">4.2 Registrar Nuevo Producto</h2>
  ${captura(capturas.productosRegistrar, 'Fig. 4.2 — Formulario Registro de Producto')}
  <p>Para registrar un nuevo producto haga clic en <strong>+ Nuevo Producto</strong> y complete los campos del formulario:</p>
  <ol>
    <li><strong>Código:</strong> Ingrese un código único alfanumérico para identificar el producto.</li>
    <li><strong>Nombre:</strong> Nombre completo y descriptivo del producto.</li>
    <li><strong>Categoría:</strong> Seleccione la categoría correspondiente del listado desplegable.</li>
    <li><strong>Descripción:</strong> Descripción opcional con características relevantes.</li>
    <li><strong>Unidad de Medida:</strong> Ingrese la unidad (unidades, kg, litros, cajas, etc.).</li>
    <li><strong>Stock Mínimo:</strong> Cantidad mínima para activar la alerta de stock bajo.</li>
    <li>Haga clic en <strong>Guardar</strong> para registrar el producto.</li>
  </ol>

  <h2 class="section-title" id="productos-editar">4.3 Editar y Eliminar Producto</h2>
  ${captura(capturas.productosEditar, 'Fig. 4.3 — Formulario Edición de Producto')}
  <p>Para modificar un producto, haga clic en el botón <strong>Editar</strong> (ícono de lápiz) en la fila correspondiente. Para eliminar un producto, haga clic en el botón <strong>Eliminar</strong> (ícono de papelera).</p>
  <div class="warning"><span class="tag-warn">ADVERTENCIA</span> Eliminar un producto es una acción irreversible. Solo elimine productos que no tengan movimientos asociados.</div>
</div>

<!-- ==================== 5. CATEGORÍAS ==================== -->
<div class="chapter" id="categorias">
  <h1 class="section-title">5. Gestión de Categorías</h1>
  <p>Las <strong>Categorías</strong> permiten organizar el catálogo de productos en grupos lógicos, facilitando la búsqueda y la generación de reportes segmentados.</p>

  <h2 class="section-title" id="categorias-listado">5.1 Listado de Categorías</h2>
  ${captura(capturas.categorias, 'Fig. 5.1 — Listado de Categorías')}

  <h2 class="section-title" id="categorias-registrar">5.2 Registrar Categoría</h2>
  ${captura(capturas.categoriasRegistrar, 'Fig. 5.2 — Formulario Registro de Categoría')}
  <p>Haga clic en <strong>+ Nueva Categoría</strong>, ingrese el nombre y una descripción opcional, luego haga clic en <strong>Guardar</strong>.</p>

  <h2 class="section-title" id="categorias-editar">5.3 Editar Categoría</h2>
  ${captura(capturas.categoriasEditar, 'Fig. 5.3 — Formulario Edición de Categoría')}
  <p>Haga clic en el botón <strong>Editar</strong> de la categoría que desea modificar, realice los cambios y guarde.</p>
</div>

<!-- ==================== 6. ENTRADAS ==================== -->
<div class="chapter" id="entradas">
  <h1 class="section-title">6. Registro de Entradas</h1>
  <p>El módulo de <strong>Entradas</strong> registra el ingreso de materiales al almacén, actualizando automáticamente el stock de los productos involucrados.</p>

  <h2 class="section-title" id="entradas-listado">6.1 Listado de Entradas</h2>
  ${captura(capturas.entradas, 'Fig. 6.1 — Listado de Entradas al Almacén')}
  <p>Se muestra el historial de todas las entradas registradas con fecha, productos, cantidades y el usuario que realizó el registro.</p>

  <h2 class="section-title" id="entradas-registrar">6.2 Registrar Nueva Entrada</h2>
  ${captura(capturas.entradasRegistrar, 'Fig. 6.2 — Formulario Nueva Entrada')}
  <ol>
    <li>Haga clic en <strong>+ Nueva Entrada</strong>.</li>
    <li>Seleccione el <strong>Producto</strong> del catálogo.</li>
    <li>Ingrese la <strong>Cantidad</strong> que ingresa al almacén.</li>
    <li>Ingrese el <strong>Proveedor</strong> (opcional) o el origen de los materiales.</li>
    <li>Agregue una <strong>Descripción</strong> u observaciones relevantes.</li>
    <li>Haga clic en <strong>Registrar</strong> para confirmar la entrada.</li>
  </ol>
  <div class="success"><span class="tag-succ">CONFIRMACIÓN</span> Al guardar, el stock del producto se incrementa automáticamente con la cantidad ingresada.</div>
</div>

<!-- ==================== 7. SALIDAS ==================== -->
<div class="chapter" id="salidas">
  <h1 class="section-title">7. Registro de Salidas</h1>
  <p>El módulo de <strong>Salidas</strong> registra el egreso de materiales del almacén hacia los diferentes departamentos o dependencias.</p>

  <h2 class="section-title" id="salidas-listado">7.1 Listado de Salidas</h2>
  ${captura(capturas.salidas, 'Fig. 7.1 — Listado de Salidas del Almacén')}

  <h2 class="section-title" id="salidas-registrar">7.2 Registrar Nueva Salida</h2>
  ${captura(capturas.salidasRegistrar, 'Fig. 7.2 — Formulario Nueva Salida')}
  <ol>
    <li>Haga clic en <strong>+ Nueva Salida</strong>.</li>
    <li>Seleccione el <strong>Producto</strong> del catálogo.</li>
    <li>Ingrese la <strong>Cantidad</strong> que sale del almacén.</li>
    <li>Indique el <strong>Destino</strong> o departamento receptor.</li>
    <li>Agregue observaciones si es necesario.</li>
    <li>Haga clic en <strong>Registrar</strong> para confirmar la salida.</li>
  </ol>
  <div class="warning"><span class="tag-warn">ADVERTENCIA</span> No se permite registrar una salida si la cantidad supera el stock disponible del producto.</div>
</div>

<!-- ==================== 8. MOVIMIENTOS ==================== -->
<div class="chapter" id="movimientos">
  <h1 class="section-title">8. Historial de Movimientos</h1>
  <p>El módulo de <strong>Movimientos</strong> consolida en una sola vista el historial completo de entradas y salidas, permitiendo rastrear cada transacción realizada en el sistema.</p>

  ${captura(capturas.movimientos, 'Fig. 8.1 — Historial de Movimientos')}

  <h2 class="section-title">8.1 Filtros Disponibles</h2>
  <table>
    <tr><th>Filtro</th><th>Descripción</th></tr>
    <tr><td>Tipo</td><td>Filtra por Entrada o Salida.</td></tr>
    <tr><td>Producto</td><td>Muestra movimientos de un producto específico.</td></tr>
    <tr><td>Fecha Inicio / Fin</td><td>Rango de fechas para acotar el historial.</td></tr>
    <tr><td>Usuario</td><td>Filtra por el usuario que registró el movimiento.</td></tr>
  </table>
</div>

<!-- ==================== 9. REPORTES ==================== -->
<div class="chapter" id="reportes">
  <h1 class="section-title">9. Reportes</h1>
  <p>El módulo de <strong>Reportes</strong> genera informes estadísticos y de inventario que facilitan la toma de decisiones y el control administrativo.</p>

  ${captura(capturas.reportes, 'Fig. 9.1 — Módulo de Reportes')}

  <h2 class="section-title">9.1 Tipos de Reportes</h2>
  <table>
    <tr><th>Reporte</th><th>Descripción</th></tr>
    <tr><td>Stock Actual</td><td>Muestra el inventario vigente con cantidades disponibles por producto.</td></tr>
    <tr><td>Entradas por Período</td><td>Resumen de ingresos en un rango de fechas seleccionado.</td></tr>
    <tr><td>Salidas por Período</td><td>Resumen de egresos en un rango de fechas seleccionado.</td></tr>
    <tr><td>Productos con Stock Bajo</td><td>Lista productos cuyo stock actual está por debajo del mínimo.</td></tr>
    <tr><td>Movimientos por Producto</td><td>Historial completo de un producto específico.</td></tr>
  </table>

  <h2 class="section-title">9.2 Exportar Reportes</h2>
  <p>Los reportes pueden exportarse en formato <strong>PDF</strong> o <strong>Excel</strong> para su archivo o distribución. Use los botones de exportación ubicados en la parte superior del reporte generado.</p>
</div>

<!-- ==================== 10. USUARIOS ==================== -->
<div class="chapter" id="usuarios">
  <h1 class="section-title">10. Gestión de Usuarios</h1>
  <p>El módulo de <strong>Usuarios</strong> (exclusivo del Administrador) permite crear y administrar las cuentas de acceso al sistema, asignando el rol correspondiente a cada usuario.</p>

  <h2 class="section-title" id="usuarios-listado">10.1 Listado de Usuarios</h2>
  ${captura(capturas.usuarios, 'Fig. 10.1 — Listado de Usuarios del Sistema')}

  <h2 class="section-title" id="usuarios-registrar">10.2 Registrar Nuevo Usuario</h2>
  ${captura(capturas.usuariosRegistrar, 'Fig. 10.2 — Formulario Registro de Usuario')}
  <ol>
    <li>Haga clic en <strong>+ Nuevo Usuario</strong>.</li>
    <li>Complete <strong>Nombre Completo</strong> y <strong>Correo Electrónico</strong>.</li>
    <li>Seleccione el <strong>Rol</strong> del usuario (Administrador, Almacén o Consultor).</li>
    <li>Establezca una <strong>Contraseña</strong> inicial.</li>
    <li>Haga clic en <strong>Guardar</strong>.</li>
  </ol>

  <h2 class="section-title" id="usuarios-editar">10.3 Editar Usuario</h2>
  ${captura(capturas.usuariosEditar, 'Fig. 10.3 — Formulario Edición de Usuario')}
  <p>Haga clic en el botón <strong>Editar</strong> (ícono de lápiz) del usuario que desea modificar. Puede actualizar el nombre, correo, rol y contraseña. Haga clic en <strong>Actualizar</strong> para guardar los cambios.</p>
  <div class="warning"><span class="tag-warn">ADVERTENCIA</span> Sea cuidadoso al cambiar el rol de un usuario: un Administrador que pierde su rol no podrá gestionar el sistema.</div>
</div>

<!-- ==================== 11. ROLES ==================== -->
<div class="chapter" id="roles">
  <h1 class="section-title">11. Roles y Permisos</h1>
  <p>El sistema implementa un modelo de <strong>control de acceso basado en roles (RBAC)</strong>. Cada usuario tiene asignado exactamente un rol que determina las acciones que puede realizar.</p>

  <table class="roles-table">
    <thead>
      <tr>
        <th style="width: 40%;">Módulo / Operación</th>
        <th style="width: 20%; text-align: center;"><span class="badge badge-admin">Administrador</span></th>
        <th style="width: 20%; text-align: center;"><span class="badge badge-almacen">Almacén</span></th>
        <th style="width: 20%; text-align: center;"><span class="badge badge-consultor">Consultor</span></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Acceso al Dashboard</strong></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
      </tr>
      <tr>
        <td><strong>Consultar Catálogo de Productos</strong></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
      </tr>
      <tr>
        <td><strong>Registrar y Editar Productos</strong></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-no">✕ Restringido</span></td>
      </tr>
      <tr>
        <td><strong>Eliminar Productos</strong></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-no">✕ Restringido</span></td>
        <td style="text-align: center;"><span class="perm-no">✕ Restringido</span></td>
      </tr>
      <tr>
        <td><strong>Registrar y Editar Categorías</strong></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-no">✕ Restringido</span></td>
      </tr>
      <tr>
        <td><strong>Eliminar Categorías</strong></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-no">✕ Restringido</span></td>
        <td style="text-align: center;"><span class="perm-no">✕ Restringido</span></td>
      </tr>
      <tr>
        <td><strong>Registrar Entradas (Stock +)</strong></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-no">✕ Restringido</span></td>
      </tr>
      <tr>
        <td><strong>Registrar Salidas (Stock -)</strong></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-no">✕ Restringido</span></td>
      </tr>
      <tr>
        <td><strong>Historial de Movimientos</strong></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
      </tr>
      <tr>
        <td><strong>Consultar y Exportar Reportes</strong></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
      </tr>
      <tr>
        <td><strong>Administrar Usuarios y Asignar Roles</strong></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-no">✕ Restringido</span></td>
        <td style="text-align: center;"><span class="perm-no">✕ Restringido</span></td>
      </tr>
      <tr>
        <td><strong>Editar Perfil y Contraseña Propia</strong></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
        <td style="text-align: center;"><span class="perm-yes">✓ Permitido</span></td>
      </tr>
    </tbody>
  </table>

  <div class="note"><span class="tag-note">NOTA</span> El Administrador tiene acceso total a todos los módulos. El rol Almacén puede gestionar el inventario pero no administrar usuarios. El Consultor solo puede visualizar información.</div>
</div>

<!-- ==================== 12. CONSULTOR ==================== -->
<div class="chapter" id="consultor">
  <h1 class="section-title">12. Perfil Consultor (Solo Lectura)</h1>
  <p>El rol <strong>Consultor</strong> tiene acceso de solo lectura al sistema. No puede registrar ni modificar ningún dato. Este perfil es ideal para directivos, auditores u otras personas que necesitan consultar información sin modificarla.</p>

  <h2 class="section-title">12.1 Dashboard del Consultor</h2>
  ${captura(capturas.dashboardConsultor, 'Fig. 12.1 — Dashboard visto por el Consultor')}

  <h2 class="section-title">12.2 Productos (Vista Consultor)</h2>
  ${captura(capturas.productosConsultor, 'Fig. 12.2 — Listado de Productos en modo Consultor (sin botones de edición)')}
  <p>En el modo Consultor, los botones de <em>Nuevo</em>, <em>Editar</em> y <em>Eliminar</em> están ocultos. El menú lateral tampoco muestra accesos a módulos de gestión restringidos.</p>

  <h2 class="section-title">12.3 Movimientos (Vista Consultor)</h2>
  ${captura(capturas.movimientosConsultor, 'Fig. 12.3 — Historial de Movimientos en modo Consultor')}
</div>

<!-- ==================== 13. SWAGGER / API REST ==================== -->
<div class="chapter" id="swagger">
  <h1 class="section-title">13. Documentación de API con Swagger / OpenAPI</h1>
  <p>El <strong>Sistema de Inventario Web</strong> incorpora una arquitectura orientada a servicios que expone una <strong>API RESTful</strong> robusta para la integración con sistemas institucionales, aplicaciones móviles y auditoría automatizada. La especificación técnica está estandarizada bajo <strong>OpenAPI 3.0</strong> y documentada visualmente mediante <strong>Swagger UI</strong>.</p>

  <h2 class="section-title" id="swagger-intro">13.1 ¿Qué es Swagger y OpenAPI 3.0?</h2>
  <p><strong>OpenAPI</strong> es la especificación estándar de la industria para describir APIs RESTful de forma agnóstica al lenguaje de programación. <strong>Swagger UI</strong> es la interfaz gráfica interactiva que renderiza dicha especificación, permitiendo a los desarrolladores y personal de TI:</p>
  <ul>
    <li><strong>Explorar el catálogo de recursos:</strong> Visualizar endpoints, métodos HTTP (GET, POST, PUT, DELETE), parámetros requeridos y esquemas de respuesta JSON.</li>
    <li><strong>Ejecutar pruebas interactivas en vivo:</strong> Enviar solicitudes directas al servidor desde el navegador mediante la función <em>"Try it out"</em> sin requerir software externo (como Postman o Insomnia).</li>
    <li><strong>Inspeccionar modelos de datos:</strong> Consultar atributos, tipos de datos (string, integer, boolean) y restricciones de validación del inventario.</li>
  </ul>

  <h2 class="section-title" id="swagger-acceso">13.2 Acceso y Autenticación con Token (Sanctum)</h2>
  <p>Para ingresar a la documentación interactiva de Swagger en su navegador web:</p>
  <div style="background: #f1f5f9; border-left: 4px solid #0f3460; padding: 10px 14px; font-family: monospace; font-size: 9.5pt; font-weight: 700; margin: 12px 0;">
    URL Swagger: https://inventario.instituto.edu.pe/api/documentation &nbsp; (o http://127.0.0.1:8000/api/documentation)
  </div>

  <p>Los endpoints de consulta y modificación del inventario están protegidos mediante <strong>Laravel Sanctum</strong>. El proceso para autorizar su sesión en la consola Swagger es el siguiente:</p>
  <ol>
    <li>Ejecute el endpoint <code>POST /api/login</code> proporcionando su usuario o correo institucional y su contraseña.</li>
    <li>En la respuesta JSON exitosa, copie el valor del campo <code>access_token</code> (token alfanumérico seguro).</li>
    <li>Haga clic en el botón verde <strong>"Authorize 🔓"</strong> ubicado en la parte superior derecha de la interfaz Swagger.</li>
    <li>En el modal emergente (<em>BearerAuth</em>), ingrese: <code>Bearer &lt;su_token_copiado&gt;</code> y presione <strong>Authorize</strong>.</li>
    <li>El candado cambiará a estado cerrado 🔒, habilitando la ejecución de peticiones autenticadas.</li>
  </ol>

  <h2 class="section-title" id="swagger-interfaz">13.3 Estructura de la Interfaz Swagger UI</h2>
  <p>A continuación se representa la interfaz interactiva de Swagger UI integrada para la gestión del inventario institucional:</p>

  <!-- MOCKUP VISUAL OFICIAL SWAGGER UI -->
  <div class="swagger-ui-card">
    <div class="swagger-header">
      <div class="swagger-brand">
        <span>{ }</span> swagger <span class="sub">supported by SmartBear</span>
      </div>
      <span class="swagger-badge-oas">OAS 3.0</span>
    </div>
    <div class="swagger-subbar">
      <div class="swagger-server-info">
        <strong>Servidor activo:</strong> <code>https://inventario.instituto.edu.pe/api</code> &nbsp;|&nbsp; <code>http://127.0.0.1:8000/api</code>
      </div>
      <div class="swagger-btn-auth">Authorize 🔓</div>
    </div>
    <div class="swagger-endpoint-list">
      <div class="swagger-tag-title">Autenticación (Auth)</div>
      <div class="swagger-endpoint-row op-post">
        <span class="method">POST</span>
        <span class="path">/api/login</span>
        <span class="desc">Autenticación y generación de Bearer Token de sesión</span>
      </div>
      <div class="swagger-endpoint-row op-post">
        <span class="method">POST</span>
        <span class="path">/api/logout</span>
        <span class="desc">Revocar token activo y cerrar sesión de la API</span>
      </div>
      <div class="swagger-endpoint-row op-get">
        <span class="method">GET</span>
        <span class="path">/api/user</span>
        <span class="desc">Consultar datos y permisos del usuario autenticado</span>
      </div>

      <div class="swagger-tag-title">Productos (Products)</div>
      <div class="swagger-endpoint-row op-get">
        <span class="method">GET</span>
        <span class="path">/api/productos</span>
        <span class="desc">Listar productos con paginación, filtros de categoría y stock</span>
      </div>
      <div class="swagger-endpoint-row op-post">
        <span class="method">POST</span>
        <span class="path">/api/productos</span>
        <span class="desc">Crear un nuevo producto con stock mínimo e identificación</span>
      </div>
      <div class="swagger-endpoint-row op-get">
        <span class="method">GET</span>
        <span class="path">/api/productos/{id}</span>
        <span class="desc">Obtener detalle completo, existencia y ficha técnica</span>
      </div>
      <div class="swagger-endpoint-row op-put">
        <span class="method">PUT</span>
        <span class="path">/api/productos/{id}</span>
        <span class="desc">Actualizar descripción, unidad y parámetros de reposición</span>
      </div>
      <div class="swagger-endpoint-row op-delete">
        <span class="method">DELETE</span>
        <span class="path">/api/productos/{id}</span>
        <span class="desc">Dar de baja producto sin transacciones (solo Administrador)</span>
      </div>

      <div class="swagger-tag-title">Categorías (Categories)</div>
      <div class="swagger-endpoint-row op-get">
        <span class="method">GET</span>
        <span class="path">/api/categorias</span>
        <span class="desc">Consultar catálogo de familias y grupos de bienes</span>
      </div>
      <div class="swagger-endpoint-row op-post">
        <span class="method">POST</span>
        <span class="path">/api/categorias</span>
        <span class="desc">Registrar nueva categoría institucional</span>
      </div>

      <div class="swagger-tag-title">Movimientos y Almacén (Movements)</div>
      <div class="swagger-endpoint-row op-get">
        <span class="method">GET</span>
        <span class="path">/api/movimientos</span>
        <span class="desc">Consultar historial de kárdex (entradas y salidas consolidadas)</span>
      </div>
      <div class="swagger-endpoint-row op-post">
        <span class="method">POST</span>
        <span class="path">/api/entradas</span>
        <span class="desc">Registrar ingreso de mercadería e incremento de stock</span>
      </div>
      <div class="swagger-endpoint-row op-post">
        <span class="method">POST</span>
        <span class="path">/api/salidas</span>
        <span class="desc">Registrar salida/despacho con validación de existencia</span>
      </div>

      <div class="swagger-tag-title">Reportes (Reports)</div>
      <div class="swagger-endpoint-row op-get">
        <span class="method">GET</span>
        <span class="path">/api/reportes/stock-bajo</span>
        <span class="desc">Alerta de productos con existencia crítica o inferior al mínimo</span>
      </div>
    </div>
  </div>

  <h2 class="section-title" id="swagger-endpoints">13.4 Catálogo de Endpoints RESTful</h2>
  <table>
    <thead>
      <tr>
        <th style="width: 14%;">Método</th>
        <th style="width: 32%;">Endpoint</th>
        <th style="width: 36%;">Operación</th>
        <th style="width: 18%;">Nivel de Acceso</th>
      </tr>
    </thead>
    <tbody>
      <tr><td><span class="perm-yes">POST</span></td><td><code>/api/login</code></td><td>Autenticación y generación de token</td><td>Público</td></tr>
      <tr><td><span class="perm-yes">POST</span></td><td><code>/api/logout</code></td><td>Cierra sesión e invalida token</td><td>Autenticado</td></tr>
      <tr><td><span class="perm-yes">GET</span></td><td><code>/api/user</code></td><td>Datos del usuario en sesión</td><td>Autenticado</td></tr>
      <tr><td><span class="perm-yes">GET</span></td><td><code>/api/productos</code></td><td>Listado de catálogo y existencias</td><td>Consultor</td></tr>
      <tr><td><span class="perm-yes">POST</span></td><td><code>/api/productos</code></td><td>Alta de nuevo bien o material</td><td>Almacén</td></tr>
      <tr><td><span class="perm-yes">GET</span></td><td><code>/api/productos/{id}</code></td><td>Ficha y detalle de existencia</td><td>Consultor</td></tr>
      <tr><td><span class="perm-yes">PUT</span></td><td><code>/api/productos/{id}</code></td><td>Modificar producto existente</td><td>Almacén</td></tr>
      <tr><td><span class="perm-no">DELETE</span></td><td><code>/api/productos/{id}</code></td><td>Eliminar producto sin movimientos</td><td>Administrador</td></tr>
      <tr><td><span class="perm-yes">GET</span></td><td><code>/api/categorias</code></td><td>Listado de familias de artículos</td><td>Consultor</td></tr>
      <tr><td><span class="perm-yes">POST</span></td><td><code>/api/categorias</code></td><td>Crear nueva categoría</td><td>Almacén</td></tr>
      <tr><td><span class="perm-yes">POST</span></td><td><code>/api/entradas</code></td><td>Registrar entrada de mercadería</td><td>Almacén</td></tr>
      <tr><td><span class="perm-yes">POST</span></td><td><code>/api/salidas</code></td><td>Registrar salida o despacho</td><td>Almacén</td></tr>
      <tr><td><span class="perm-yes">GET</span></td><td><code>/api/movimientos</code></td><td>Kárdex completo con filtros</td><td>Consultor</td></tr>
      <tr><td><span class="perm-yes">GET</span></td><td><code>/api/reportes/stock-bajo</code></td><td>Reporte de productos en alerta</td><td>Consultor</td></tr>
    </tbody>
  </table>

  <h2 class="section-title" id="swagger-testing">13.5 Pruebas en Vivo con "Try it out"</h2>
  <p>La consola de Swagger permite probar peticiones en tiempo real sin salir del navegador:</p>
  <ol>
    <li>Haga clic sobre el bloque del endpoint que desea probar (ej. <code>GET /api/productos/{id}</code>).</li>
    <li>Presione el botón <strong>"Try it out"</strong> ubicado a la derecha.</li>
    <li>Complete los parámetros requeridos (por ejemplo, en el campo <code>id</code> ingrese <code>1</code>).</li>
    <li>Haga clic en el botón azul <strong>"Execute"</strong>.</li>
    <li>Swagger ejecutará la solicitud HTTP real, generará el comando <code>curl</code> equivalente y presentará la respuesta del servidor:</li>
  </ol>

  <p><strong>Ejemplo de Respuesta Exitosa (200 OK):</strong></p>
  <pre class="code-json">{
  "status": "success",
  "data": {
    "id": 1,
    "codigo": "PROD-001",
    "nombre": "Papel Bond A4 75g",
    "categoria": "Útiles de Oficina",
    "stock_actual": 140,
    "stock_minimo": 20,
    "unidad_medida": "millar",
    "estado": "activo",
    "created_at": "2026-11-19T10:00:00.000000Z"
  }
}</pre>

  <h2 class="section-title" id="swagger-codigos">13.6 Códigos de Respuesta HTTP Estándar</h2>
  <table>
    <thead>
      <tr>
        <th style="width: 18%;">Código HTTP</th>
        <th style="width: 30%;">Estado</th>
        <th style="width: 52%;">Descripción en la API</th>
      </tr>
    </thead>
    <tbody>
      <tr><td><strong>200 OK</strong></td><td>Petición Exitosa</td><td>La consulta o actualización se completó satisfactoriamente.</td></tr>
      <tr><td><strong>201 Created</strong></td><td>Registro Creado</td><td>El producto, entrada o categoría fue dado de alta en la base de datos.</td></tr>
      <tr><td><strong>400 Bad Request</strong></td><td>Solicitud Incorrecta</td><td>Parámetros obligatorios faltantes o sintaxis JSON errónea.</td></tr>
      <tr><td><strong>401 Unauthorized</strong></td><td>No Autorizado</td><td>Token Bearer ausente o expirado. Debe autorizarse en Swagger.</td></tr>
      <tr><td><strong>403 Forbidden</strong></td><td>Acceso Restringido</td><td>El rol del usuario autenticado no posee permiso para la operación.</td></tr>
      <tr><td><strong>404 Not Found</strong></td><td>Recurso No Hallado</td><td>El identificador solicitado no existe en la base de datos.</td></tr>
      <tr><td><strong>422 Unprocessable</strong></td><td>Error de Validación</td><td>Fallo en reglas de negocio (código duplicado, salida excede stock).</td></tr>
      <tr><td><strong>500 Server Error</strong></td><td>Error del Servidor</td><td>Excepción interna no controlada. Requiere revisión técnica de logs.</td></tr>
    </tbody>
  </table>

  <div class="note"><span class="tag-note">NOTA</span> La consola interactiva de Swagger está orientada al equipo de desarrollo, auditores de sistemas y administradores de TI. No comparta sus tokens de acceso personal con terceros.</div>
</div>

<!-- ==================== 14. FAQ ==================== -->
<div class="chapter" id="faq">
  <h1 class="section-title">14. Preguntas Frecuentes</h1>

  <h2 class="section-title">¿Cómo recupero mi contraseña?</h2>
  <p>Contacte al administrador del sistema para que restablezca su contraseña desde el módulo de Gestión de Usuarios.</p>

  <h2 class="section-title">¿Puedo registrar una salida si no hay stock?</h2>
  <p>No. El sistema valida automáticamente que haya stock suficiente antes de permitir registrar una salida.</p>

  <h2 class="section-title">¿Cómo sé si un producto está en stock bajo?</h2>
  <p>El dashboard muestra alertas cuando un producto cae por debajo de su stock mínimo configurado. También puede generarse un reporte de "Productos con Stock Bajo" desde el módulo de Reportes.</p>

  <h2 class="section-title">¿Puedo eliminar un movimiento ya registrado?</h2>
  <p>No. Los movimientos son registros contables permanentes. Si existe un error, debe registrar una operación correctiva (entrada o salida de ajuste).</p>

  <h2 class="section-title">¿Con qué navegadores funciona el sistema?</h2>
  <p>El sistema es compatible con Google Chrome, Mozilla Firefox y Microsoft Edge en sus versiones actuales. Se recomienda usar Chrome para la mejor experiencia.</p>

  <h2 class="section-title">¿Cómo exporto un reporte?</h2>
  <p>En el módulo de Reportes, genere el reporte deseado y use los botones <strong>Exportar PDF</strong> o <strong>Exportar Excel</strong> para descargarlo.</p>

  <div style="margin-top:40px; padding:20px; background:#1a1a2e; color:#a8dadc; border-radius:8px; text-align:center; font-size:10pt;">
    <strong>Sistema de Inventario Web</strong> — Manual de Usuario v1.0<br/>
    Departamento de Tecnología de la Información<br/>
    Para soporte técnico comuníquese con el administrador del sistema.
  </div>
</div>

</body>
</html>`;

(async () => {
  console.log('🚀 Iniciando generación de PDF...');

  const browser = await puppeteer.launch({
    executablePath: CHROME,
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage']
  });

  const page = await browser.newPage();
  page.setDefaultTimeout(60000);
  await page.setContent(html, { waitUntil: 'load' });

  // Esperar a que las imágenes carguen
  await page.evaluate(() => {
    return Promise.all(
      Array.from(document.images).map(img =>
        img.complete ? Promise.resolve() : new Promise(r => { img.onload = r; img.onerror = r; })
      )
    );
  });

  await page.pdf({
    path: OUTPUT,
    format: 'A4',
    printBackground: true,
    margin: { top: '22mm', bottom: '22mm', left: '18mm', right: '18mm' },
    displayHeaderFooter: true,
    headerTemplate: '<div style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 8pt; color: #888; width: 100%; padding: 0 18mm; display: flex; justify-content: space-between; border-bottom: 1px solid #e0e0e0; padding-bottom: 4px;"><span style="color: #0f3460; font-weight: 700;">Sistema de Inventario Web</span><span>Manual de Usuario Institucional</span></div>',
    footerTemplate: '<div style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 8pt; color: #888; width: 100%; padding: 0 18mm; display: flex; justify-content: space-between; border-top: 1px solid #e0e0e0; padding-top: 4px;"><span>Departamento de TI — Versión 1.0</span><span>Página <span class="pageNumber"></span> de <span class="totalPages"></span></span></div>',
  });

  await browser.close();

  const stat = require('fs').statSync(OUTPUT);
  console.log(`✅ PDF generado: ${OUTPUT}`);
  console.log(`   Tamaño: ${(stat.size / 1024 / 1024).toFixed(2)} MB`);
})();
