/**
 * build-der-pdf.cjs
 * Genera el PDF RESUMIDO del Diagrama Entidad - Relación (DER)
 * Enfocado directamente en la relación visual de las tablas y cardinalidad.
 */

const puppeteer = require('puppeteer-core');
const fs = require('fs');
const path = require('path');

const CHROME = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const LOGOS_DIR = path.join(__dirname, '..', 'docs', 'logos');
const OUTPUT = path.join(__dirname, '..', 'DIAGRAMA_ENTIDAD_RELACION.pdf');

function imgBase64(filename) {
    const fp = path.join(LOGOS_DIR, filename);
    if (!fs.existsSync(fp)) return '';
    const buf = fs.readFileSync(fp);
    return 'data:image/png;base64,' + buf.toString('base64');
}

const logoInstituto = imgBase64('logo-instituto.png');
const logoCarrera = imgBase64('logo-carrera.png');

const html = `<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Diagrama Entidad-Relación - Resumen Ejecutivo</title>
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<style>
  @page {
    size: A4 portrait;
    margin: 15mm 15mm 15mm 15mm;
  }
  * { box-sizing: border-box; }
  body {
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
    color: #1e293b;
    font-size: 9pt;
    line-height: 1.45;
    margin: 0;
    padding: 0;
    background: #fff;
  }
  .page-break {
    page-break-before: always;
  }
  .avoid-break {
    page-break-inside: avoid;
  }

  /* ========== ENCABEZADO INSTITUCIONAL COMPACTO ========== */
  .inst-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2.5px solid #0f3460;
    padding-bottom: 12px;
    margin-bottom: 16px;
  }
  .inst-logos {
    display: flex;
    align-items: center;
    gap: 14px;
  }
  .inst-logos img {
    height: 52px;
    width: auto;
    object-fit: contain;
  }
  .inst-text {
    flex-grow: 1;
    margin-left: 14px;
  }
  .inst-title {
    font-size: 11pt;
    font-weight: 800;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin: 0;
  }
  .inst-sub {
    font-size: 8.5pt;
    font-weight: 600;
    color: #e94560;
    margin: 0;
  }
  .inst-career {
    font-size: 8pt;
    font-weight: 700;
    color: #0284c7;
    margin: 0;
  }
  .inst-badge {
    background: #0f3460;
    color: #ffffff;
    font-size: 8pt;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 6px;
    text-align: right;
    white-space: nowrap;
  }

  /* ========== TÍTULO DE SECCIÓN ========== */
  .sec-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f1f5f9;
    border-left: 4px solid #0284c7;
    padding: 6px 12px;
    border-radius: 0 6px 6px 0;
    margin: 14px 0 10px 0;
  }
  .sec-heading h2 {
    font-size: 11pt;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }
  .sec-heading .badge-pill {
    background: #0284c7;
    color: #fff;
    font-size: 7.5pt;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 10px;
  }

  p.lead-text {
    font-size: 8.5pt;
    color: #475569;
    margin: 0 0 10px 0;
    text-align: justify;
  }

  /* ========== TABLAS RESUMEN ========== */
  table.compact-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
    font-size: 8pt;
    background: #fff;
  }
  table.compact-table th {
    background: #0f3460;
    color: #fff;
    font-weight: 700;
    text-align: left;
    padding: 6px 8px;
    border: 1px solid #0f3460;
    font-size: 8pt;
  }
  table.compact-table td {
    padding: 5px 8px;
    border: 1px solid #cbd5e1;
    color: #334155;
    vertical-align: middle;
  }
  table.compact-table tr:nth-child(even) td {
    background: #f8fafc;
  }

  /* Badges */
  .badge {
    display: inline-block;
    padding: 2px 5px;
    border-radius: 4px;
    font-size: 7pt;
    font-weight: 700;
    text-transform: uppercase;
    font-family: monospace;
  }
  .badge-pk { background: #fee2e2; color: #b91c1c; border: 1px solid #f87171; }
  .badge-fk { background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; }
  .badge-uk { background: #fef3c7; color: #b45309; border: 1px solid #fcd34d; }
  .badge-card { background: #ede9fe; color: #6d28d9; border: 1px solid #c4b5fd; font-weight: 800; }
  .badge-rule { background: #ffe4e6; color: #be123c; border: 1px solid #fda4af; font-weight: 700; }

  /* Diagrama Mermaid */
  .mermaid-wrapper {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 10px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    margin-bottom: 12px;
  }
  .mermaid svg {
    max-width: 100% !important;
    height: auto !important;
  }

  /* Cuadros de flujo relacional */
  .flow-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 12px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 8pt;
  }
  .flow-left {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .table-tag {
    font-weight: 800;
    color: #0f3460;
    background: #e2e8f0;
    padding: 3px 8px;
    border-radius: 4px;
    font-family: monospace;
  }
  .arrow-tag {
    color: #64748b;
    font-weight: 700;
  }
  .flow-meta {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  /* Pie de documento */
  .doc-footer {
    border-top: 1.5px solid #e2e8f0;
    padding-top: 8px;
    margin-top: 14px;
    display: flex;
    justify-content: space-between;
    font-size: 7.5pt;
    color: #64748b;
  }
</style>
</head>
<body>

<!-- ==================== PÁGINA 1: RESUMEN EJECUTIVO ==================== -->
<div class="inst-header">
  <div class="inst-logos">
    <img src="${logoInstituto}" alt="Logo Instituto">
    <img src="${logoCarrera}" alt="Logo Carrera">
  </div>
  <div class="inst-text">
    <div class="inst-title">IESTP "César A. Guardia Mayorga" — Coracora</div>
    <div class="inst-sub">Programa de Estudios: Arquitectura de Plataformas y Servicios TI (APSTI)</div>
    <div class="inst-career">Sistema Informático de Gestión y Control de Inventario Web</div>
  </div>
  <div class="inst-badge">
    DIAGRAMA E-R<br/>RESUMEN OFICIAL
  </div>
</div>

<div class="sec-heading">
  <h2>1. Resumen Ejecutivo de la Base de Datos</h2>
  <span class="badge-pill">MariaDB 10.11 · InnoDB · 3FN</span>
</div>

<p class="lead-text">
  La base de datos del sistema está estructurada bajo el estándar de la <strong>Tercera Forma Normal (3FN)</strong> con motor transaccional <strong>InnoDB</strong>. La arquitectura separa claramente las funciones de seguridad y autenticación, catalogación jerárquica de bienes y el registro inmutable del kárdex contable.
</p>

<table class="compact-table">
  <thead>
    <tr>
      <th style="width: 18%;">Entidad (Tabla)</th>
      <th style="width: 15%;">Módulo</th>
      <th style="width: 12%;">Clave Primaria</th>
      <th style="width: 20%;">Claves Foráneas</th>
      <th style="width: 35%;">Función en el Sistema</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><strong><code>roles</code></strong></td>
      <td>Seguridad RBAC</td>
      <td><span class="badge badge-pk">PK</span> id</td>
      <td>—</td>
      <td>Perfiles de acceso: Administrador, Encargado de almacén y Consultor.</td>
    </tr>
    <tr>
      <td><strong><code>users</code></strong></td>
      <td>Autenticación</td>
      <td><span class="badge badge-pk">PK</span> id</td>
      <td><span class="badge badge-fk">FK</span> role_id ➔ <code>roles</code></td>
      <td>Operadores del sistema con login por username o email institucional.</td>
    </tr>
    <tr>
      <td><strong><code>categorias</code></strong></td>
      <td>Catálogo</td>
      <td><span class="badge badge-pk">PK</span> id</td>
      <td>—</td>
      <td>Familias de clasificación (Cómputo, Papelería, Herramientas, etc.).</td>
    </tr>
    <tr>
      <td><strong><code>productos</code></strong></td>
      <td>Inventario</td>
      <td><span class="badge badge-pk">PK</span> id</td>
      <td><span class="badge badge-fk">FK</span> category_id ➔ <code>categorias</code></td>
      <td>Bienes físicos con código único, stock actual y alerta de stock mínimo.</td>
    </tr>
    <tr>
      <td><strong><code>movimientos</code></strong></td>
      <td>Kárdex Central</td>
      <td><span class="badge badge-pk">PK</span> id</td>
      <td>
        <span class="badge badge-fk">FK</span> product_id ➔ <code>productos</code><br/>
        <span class="badge badge-fk">FK</span> user_id ➔ <code>users</code>
      </td>
      <td>Asientos inmutables de entradas (compras) y salidas (aulas/talleres).</td>
    </tr>
  </tbody>
</table>

<div class="sec-heading">
  <h2>2. Resumen Rápido de Relaciones entre Tablas</h2>
  <span class="badge-pill">Cardinalidad 1 a Muchos (1:N)</span>
</div>

<!-- Flujo 1 -->
<div class="flow-card">
  <div class="flow-left">
    <span class="table-tag">roles</span>
    <span class="arrow-tag">─── 1 : N ───▶</span>
    <span class="table-tag">users</span>
  </div>
  <div class="flow-meta">
    <span>Un rol se asigna a múltiples usuarios</span>
    <span class="badge badge-card">1:N</span>
    <span class="badge badge-rule">ON DELETE SET NULL</span>
  </div>
</div>

<!-- Flujo 2 -->
<div class="flow-card">
  <div class="flow-left">
    <span class="table-tag">categorias</span>
    <span class="arrow-tag">─── 1 : N ───▶</span>
    <span class="table-tag">productos</span>
  </div>
  <div class="flow-meta">
    <span>Una categoría agrupa múltiples productos</span>
    <span class="badge badge-card">1:N</span>
    <span class="badge badge-rule">ON DELETE RESTRICT</span>
  </div>
</div>

<!-- Flujo 3 -->
<div class="flow-card">
  <div class="flow-left">
    <span class="table-tag">productos</span>
    <span class="arrow-tag">─── 1 : N ───▶</span>
    <span class="table-tag">movimientos</span>
  </div>
  <div class="flow-meta">
    <span>Un producto registra múltiples movimientos de kárdex</span>
    <span class="badge badge-card">1:N</span>
    <span class="badge badge-rule">ON DELETE RESTRICT</span>
  </div>
</div>

<!-- Flujo 4 -->
<div class="flow-card">
  <div class="flow-left">
    <span class="table-tag">users</span>
    <span class="arrow-tag">─── 1 : N ───▶</span>
    <span class="table-tag">movimientos</span>
  </div>
  <div class="flow-meta">
    <span>Un usuario efectúa y firma múltiples movimientos</span>
    <span class="badge badge-card">1:N</span>
    <span class="badge badge-rule">ON DELETE RESTRICT</span>
  </div>
</div>

<!-- Flujo 5 -->
<div class="flow-card">
  <div class="flow-left">
    <span class="table-tag">users</span>
    <span class="arrow-tag">─── 1 : N ───▶</span>
    <span class="table-tag">sessions</span> / <span class="table-tag">tokens</span>
  </div>
  <div class="flow-meta">
    <span>Un usuario genera sesiones web y tokens Bearer API</span>
    <span class="badge badge-card">1:N</span>
    <span class="badge badge-rule">ON DELETE CASCADE</span>
  </div>
</div>

<div class="doc-footer">
  <span>IESTP "César A. Guardia Mayorga" · Coracora, Parinacochas, Ayacucho</span>
  <span>Página 1 de 2</span>
</div>

<!-- ==================== PÁGINA 2: DIAGRAMA E-R VISUAL ==================== -->
<div class="page-break"></div>

<div class="inst-header">
  <div class="inst-logos">
    <img src="${logoInstituto}" alt="Logo Instituto">
    <img src="${logoCarrera}" alt="Logo Carrera">
  </div>
  <div class="inst-text">
    <div class="inst-title">Diagrama Entidad - Relación (DER Gráfico)</div>
    <div class="inst-sub">Mapeo Visual de Tablas, Llaves y Enlaces de Integridad</div>
    <div class="inst-career">Base de Datos Institucional del Sistema de Inventario</div>
  </div>
  <div class="inst-badge">
    VISTA VECTORIAL<br/>COMPLETA
  </div>
</div>

<div class="sec-heading">
  <h2>Diagrama Entidad - Relación Completo</h2>
  <span class="badge-pill">Modelo Lógico - Físico</span>
</div>

<!-- DIAGRAMA MERMAID RENDERIZADO -->
<div class="mermaid-wrapper">
  <div class="mermaid">
erDiagram
    ROLES ||--o{ USERS : "1:N (role_id)"
    CATEGORIAS ||--o{ PRODUCTOS : "1:N (category_id)"
    PRODUCTOS ||--o{ MOVIMIENTOS : "1:N (product_id)"
    USERS ||--o{ MOVIMIENTOS : "1:N (user_id)"
    USERS ||--o{ SESSIONS : "1:N (user_id)"
    USERS ||--o{ PERSONAL_ACCESS_TOKENS : "1:N (tokenable_id)"

    ROLES {
        bigint id PK "Clave Primaria"
        string name "Nombre del Rol"
        string slug UK "Identificador Unico"
    }

    USERS {
        bigint id PK "Clave Primaria"
        string name "Nombres y Apellidos"
        string username UK "Usuario para Login"
        string email UK "Correo Institucional"
        string password "Hash Bcrypt"
        bigint role_id FK "FK a roles"
    }

    CATEGORIAS {
        bigint id PK "Clave Primaria"
        string name UK "Familia de Bienes"
        string description "Detalle opcional"
    }

    PRODUCTOS {
        bigint id PK "Clave Primaria"
        string code UK "Codigo de Barras"
        string name "Nombre del Producto"
        bigint category_id FK "FK a categorias"
        decimal price "Precio Unitario"
        int stock "Stock Actual"
        int stock_min "Umbral Minimo Alerta"
        enum status "activo o inactivo"
    }

    MOVIMIENTOS {
        bigint id PK "Clave Primaria (Kardex)"
        bigint product_id FK "FK a productos"
        bigint user_id FK "FK a users"
        enum type "entrada o salida"
        int quantity "Cantidad Operada"
        date date "Fecha del Movimiento"
        string observation "Comprobante / Destino"
    }

    SESSIONS {
        string id PK "Token Sesion Web"
        bigint user_id FK "FK a users"
        string ip_address "IP Cliente"
        text user_agent "Navegador"
        int last_activity "Timestamp"
    }

    PERSONAL_ACCESS_TOKENS {
        bigint id PK "Clave Primaria"
        bigint tokenable_id FK "FK a users"
        string token UK "Hash Token API"
        timestamp expires_at "Expiracion"
    }
  </div>
</div>

<div class="sec-heading">
  <h2>3. Matriz de Integridad y Reglas de Negocio</h2>
  <span class="badge-pill">Protección de Datos</span>
</div>

<table class="compact-table">
  <thead>
    <tr>
      <th style="width: 20%;">Relación</th>
      <th style="width: 25%;">Clave Foránea ➔ Primaria</th>
      <th style="width: 15%;">Comportamiento</th>
      <th style="width: 40%;">Garantía del Sistema</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>Categoría ➔ Producto</strong></td>
      <td><code>productos.category_id</code> ➔ <code>categorias.id</code></td>
      <td><span class="badge badge-rule">RESTRICT</span></td>
      <td>No se puede borrar una categoría si contiene productos inventariados.</td>
    </tr>
    <tr>
      <td><strong>Producto ➔ Kárdex</strong></td>
      <td><code>movimientos.product_id</code> ➔ <code>productos.id</code></td>
      <td><span class="badge badge-rule">RESTRICT</span></td>
      <td>Protege la auditoría: un producto con historial contable no puede eliminarse.</td>
    </tr>
    <tr>
      <td><strong>Usuario ➔ Kárdex</strong></td>
      <td><code>movimientos.user_id</code> ➔ <code>users.id</code></td>
      <td><span class="badge badge-rule">RESTRICT</span></td>
      <td>Preserva la firma y responsabilidad del operador en cada entrada/salida.</td>
    </tr>
    <tr>
      <td><strong>Rol ➔ Usuario</strong></td>
      <td><code>users.role_id</code> ➔ <code>roles.id</code></td>
      <td><span class="badge badge-pk">SET NULL</span></td>
      <td>Si se reclasifica un rol, la cuenta del usuario no se borra accidentalmente.</td>
    </tr>
    <tr>
      <td><strong>Usuario ➔ Sesiones</strong></td>
      <td><code>sessions.user_id</code> ➔ <code>users.id</code></td>
      <td><span class="badge badge-uk">CASCADE</span></td>
      <td>Si se elimina un usuario, se cierran automáticamente todas sus sesiones activas.</td>
    </tr>
  </tbody>
</table>

<div class="doc-footer">
  <span>IESTP "César A. Guardia Mayorga" · Coracora, Parinacochas, Ayacucho</span>
  <span>Página 2 de 2</span>
</div>

<script>
  mermaid.initialize({
    startOnLoad: true,
    theme: 'base',
    themeVariables: {
      primaryColor: '#e0f2fe',
      primaryTextColor: '#0f172a',
      primaryBorderColor: '#0284c7',
      lineColor: '#0f3460',
      secondaryColor: '#f1f5f9',
      tertiaryColor: '#ffffff'
    },
    er: {
      useMaxWidth: false,
      entityPadding: 15,
      stroke: '#0f3460',
      fontSize: 10
    }
  });
</script>
</body>
</html>
`;

(async () => {
    console.log('🚀 Iniciando compilación de PDF Resumido DER...');

    const browser = await puppeteer.launch({
        executablePath: CHROME,
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage']
    });

    const page = await browser.newPage();
    page.setDefaultTimeout(60000);
    await page.setContent(html, { waitUntil: 'networkidle2' });

    // Esperar a que Mermaid renderice el SVG
    await page.waitForSelector('.mermaid svg', { timeout: 15000 });
    await new Promise(r => setTimeout(r, 1200));

    await page.pdf({
        path: OUTPUT,
        format: 'A4',
        printBackground: true,
        margin: { top: '12mm', bottom: '12mm', left: '14mm', right: '14mm' },
        displayHeaderFooter: false
    });

    await browser.close();

    const stat = fs.statSync(OUTPUT);
    console.log(`✅ PDF Resumido DER generado exitosamente: ${OUTPUT}`);
    console.log(`   Tamaño: ${(stat.size / 1024).toFixed(2)} KB`);
})();
