const puppeteer = require('puppeteer-core');
const fs = require('fs');
const CHROME = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

(async () => {
  const browser = await puppeteer.launch({ executablePath: CHROME, headless: 'new' });
  const page = await browser.newPage();
  
  const testHtml = `<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
    body { font-family: 'Segoe UI', Arial, 'Segoe UI Emoji', sans-serif; font-size: 10.5pt; color: #1a1a2e; }
    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    th { background: #0f3460; color: #fff; padding: 10px 12px; text-align: left; font-size: 10pt; }
    td { padding: 8px 12px; border-bottom: 1px solid #dee2e6; font-size: 9.5pt; }
    tr:nth-child(even) td { background: #f8f9fa; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 8.5pt; font-weight: 600; }
    .badge-admin { background: #e94560; color: #fff; }
    .badge-almacen { background: #0f3460; color: #fff; }
    .badge-consultor { background: #16213e; color: #a8dadc; }
    .perm-yes { background-color: #e8f5e9; color: #2e7d32; font-weight: 700; padding: 3px 10px; border-radius: 4px; display: inline-block; font-size: 8.5pt; }
    .perm-no { background-color: #ffebee; color: #c62828; font-weight: 700; padding: 3px 10px; border-radius: 4px; display: inline-block; font-size: 8.5pt; }
  </style></head><body>
    <h2>11. Roles y Permisos</h2>
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
  </body></html>`;

  await page.setContent(testHtml, { waitUntil: 'networkidle0' });
  await page.pdf({ path: 'tools/test-table.pdf', format: 'A4', printBackground: true });
  await page.screenshot({ path: 'tools/test-table.png' });
  await browser.close();
  console.log('PDF and PNG saved successfully');
})();
