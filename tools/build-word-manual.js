/**
 * Genera el Manual de Usuario en formato Microsoft Word (.docx)
 * con diseño profesional, tablas con estilo e imágenes reales del sistema.
 */

import fs from 'node:fs';
import path from 'node:path';
import {
    Document,
    Packer,
    Paragraph,
    TextRun,
    HeadingLevel,
    ImageRun,
    Table,
    TableRow,
    TableCell,
    WidthType,
    AlignmentType,
    BorderStyle,
    ShadingType,
    PageBreak,
    Header,
    Footer,
    PageNumber
} from 'docx';

const CAPTURAS_DIR = path.join(process.cwd(), 'docs', 'capturas');
const OUTPUT_PATH = path.join(process.cwd(), 'MANUAL_DE_USUARIO.docx');

// Colores corporativos
const COLOR_PRIMARY = '0d6efd';
const COLOR_SECONDARY = '495057';
const COLOR_DARK = '212529';
const COLOR_MUTED = '6c757d';
const COLOR_LIGHT_BG = 'f8f9fa';
const COLOR_BORDER = 'dee2e6';
const COLOR_SUCCESS = '198754';
const COLOR_DANGER = 'dc3545';
const COLOR_WARNING = 'ffc107';

function pTitle(text) {
    return new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 240, after: 120 },
        children: [
            new TextRun({
                text,
                bold: true,
                size: 52, // 26pt
                color: COLOR_PRIMARY,
                font: 'Calibri',
            }),
        ],
    });
}

function pSubtitle(text) {
    return new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 360 },
        children: [
            new TextRun({
                text,
                size: 28, // 14pt
                color: COLOR_MUTED,
                font: 'Calibri',
            }),
        ],
    });
}

function pHeading1(text) {
    return new Paragraph({
        heading: HeadingLevel.HEADING_1,
        spacing: { before: 360, after: 140 },
        children: [
            new TextRun({
                text,
                bold: true,
                size: 32, // 16pt
                color: COLOR_PRIMARY,
                font: 'Calibri',
            }),
        ],
    });
}

function pHeading2(text) {
    return new Paragraph({
        heading: HeadingLevel.HEADING_2,
        spacing: { before: 260, after: 100 },
        children: [
            new TextRun({
                text,
                bold: true,
                size: 26, // 13pt
                color: COLOR_DARK,
                font: 'Calibri',
            }),
        ],
    });
}

function pHeading3(text) {
    return new Paragraph({
        heading: HeadingLevel.HEADING_3,
        spacing: { before: 180, after: 80 },
        children: [
            new TextRun({
                text,
                bold: true,
                size: 22, // 11pt
                color: COLOR_SECONDARY,
                font: 'Calibri',
            }),
        ],
    });
}

function pBody(text, options = {}) {
    return new Paragraph({
        spacing: { before: 60, after: 80 },
        alignment: options.alignment || AlignmentType.LEFT,
        children: [
            new TextRun({
                text,
                size: 22, // 11pt
                color: options.color || COLOR_DARK,
                bold: options.bold || false,
                italics: options.italics || false,
                font: 'Calibri',
            }),
        ],
    });
}

function pBullet(boldPrefix, restOfText) {
    return new Paragraph({
        bullet: { level: 0 },
        spacing: { before: 40, after: 40 },
        children: [
            new TextRun({
                text: boldPrefix,
                bold: true,
                size: 22,
                color: COLOR_DARK,
                font: 'Calibri',
            }),
            new TextRun({
                text: ' ' + restOfText,
                size: 22,
                color: COLOR_DARK,
                font: 'Calibri',
            }),
        ],
    });
}

function pCallout(title, message, borderColor = COLOR_PRIMARY, bgColor = 'e7f1ff') {
    return new Table({
        width: { size: 100, type: WidthType.PERCENTAGE },
        rows: [
            new TableRow({
                children: [
                    new TableCell({
                        shading: { fill: bgColor, type: ShadingType.CLEAR },
                        borders: {
                            left: { style: BorderStyle.SINGLE, size: 24, color: borderColor },
                            top: { style: BorderStyle.NONE },
                            right: { style: BorderStyle.NONE },
                            bottom: { style: BorderStyle.NONE },
                        },
                        margins: { top: 120, bottom: 120, left: 160, right: 160 },
                        children: [
                            new Paragraph({
                                spacing: { after: 60 },
                                children: [
                                    new TextRun({ text: title, bold: true, size: 22, color: borderColor, font: 'Calibri' }),
                                ],
                            }),
                            new Paragraph({
                                spacing: { after: 0 },
                                children: [
                                    new TextRun({ text: message, size: 21, color: COLOR_DARK, font: 'Calibri' }),
                                ],
                            }),
                        ],
                    }),
                ],
            }),
        ],
    });
}

function imageFigure(filename, caption, width = 560, height = 388) {
    const filePath = path.join(CAPTURAS_DIR, filename);
    if (!fs.existsSync(filePath)) {
        return [
            pBody(`[Captura no encontrada: ${filename}]`, { italics: true, color: COLOR_MUTED }),
        ];
    }

    const imageBuffer = fs.readFileSync(filePath);

    return [
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 140, after: 60 },
            children: [
                new ImageRun({
                    data: imageBuffer,
                    transformation: {
                        width,
                        height,
                    },
                }),
            ],
        }),
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 0, after: 160 },
            children: [
                new TextRun({
                    text: caption,
                    italics: true,
                    size: 19, // 9.5pt
                    color: COLOR_MUTED,
                    font: 'Calibri',
                }),
            ],
        }),
    ];
}

function buildPrivilegesTable() {
    const headers = ['Módulo / Operación', 'Administrador', 'Encargado Almacén', 'Consultor'];
    const rows = [
        ['Acceso al Dashboard', '✔ Permitido', '✔ Permitido', '✔ Permitido'],
        ['Consultar Catálogo de Productos', '✔ Permitido', '✔ Permitido', '✔ Permitido'],
        ['Registrar y Editar Productos', '✔ Permitido', '✔ Permitido', '✖ Restringido'],
        ['Eliminar Productos', '✔ Permitido', '✖ Restringido', '✖ Restringido'],
        ['Registrar y Editar Categorías', '✔ Permitido', '✔ Permitido', '✖ Restringido'],
        ['Eliminar Categorías', '✔ Permitido', '✖ Restringido', '✖ Restringido'],
        ['Registrar Entradas (Stock +)', '✔ Permitido', '✔ Permitido', '✖ Restringido'],
        ['Registrar Salidas (Stock -)', '✔ Permitido', '✔ Permitido', '✖ Restringido'],
        ['Editar / Eliminar Movimientos', '✔ Permitido', '✔ Permitido', '✖ Restringido'],
        ['Consultar Historial de Movimientos', '✔ Permitido', '✔ Permitido', '✔ Permitido'],
        ['Consultar e Imprimir Reportes', '✔ Permitido', '✔ Permitido', '✔ Permitido'],
        ['Administrar Usuarios y Asignar Roles', '✔ Permitido', '✖ Restringido', '✖ Restringido'],
        ['Editar Mi Propio Perfil y Contraseña', '✔ Permitido', '✔ Permitido', '✔ Permitido'],
    ];

    const tableRows = [
        new TableRow({
            tableHeader: true,
            children: headers.map((headerText, i) => new TableCell({
                width: { size: i === 0 ? 46 : 18, type: WidthType.PERCENTAGE },
                shading: { fill: '0d6efd', type: ShadingType.CLEAR },
                borders: {
                    top: { style: BorderStyle.SINGLE, size: 8, color: COLOR_BORDER },
                    bottom: { style: BorderStyle.SINGLE, size: 16, color: COLOR_PRIMARY },
                    left: { style: BorderStyle.SINGLE, size: 4, color: COLOR_BORDER },
                    right: { style: BorderStyle.SINGLE, size: 4, color: COLOR_BORDER },
                },
                margins: { top: 100, bottom: 100, left: 100, right: 100 },
                children: [
                    new Paragraph({
                        alignment: i === 0 ? AlignmentType.LEFT : AlignmentType.CENTER,
                        children: [
                            new TextRun({ text: headerText, bold: true, color: 'ffffff', size: 21, font: 'Calibri' }),
                        ],
                    }),
                ],
            })),
        }),
        ...rows.map((row, rIdx) => new TableRow({
            children: row.map((cellText, cIdx) => {
                const isPermitted = cellText.includes('✔');
                return new TableCell({
                    shading: { fill: rIdx % 2 === 0 ? 'ffffff' : COLOR_LIGHT_BG, type: ShadingType.CLEAR },
                    borders: {
                        top: { style: BorderStyle.SINGLE, size: 4, color: COLOR_BORDER },
                        bottom: { style: BorderStyle.SINGLE, size: 4, color: COLOR_BORDER },
                        left: { style: BorderStyle.SINGLE, size: 4, color: COLOR_BORDER },
                        right: { style: BorderStyle.SINGLE, size: 4, color: COLOR_BORDER },
                    },
                    margins: { top: 80, bottom: 80, left: 100, right: 100 },
                    children: [
                        new Paragraph({
                            alignment: cIdx === 0 ? AlignmentType.LEFT : AlignmentType.CENTER,
                            children: [
                                new TextRun({
                                    text: cellText,
                                    size: 20,
                                    bold: cIdx === 0,
                                    color: cIdx === 0 ? COLOR_DARK : (isPermitted ? COLOR_SUCCESS : COLOR_DANGER),
                                    font: 'Calibri',
                                }),
                            ],
                        }),
                    ],
                });
            }),
        })),
    ];

    return new Table({
        width: { size: 100, type: WidthType.PERCENTAGE },
        rows: tableRows,
    });
}

async function generateManual() {
    console.log('Generando documento Word...');

    const doc = new Document({
        styles: {
            default: {
                document: {
                    run: {
                        font: 'Calibri',
                        size: 22,
                        color: COLOR_DARK,
                    },
                },
            },
        },
        sections: [
            // ==================== PORTADA ====================
            {
                properties: {
                    page: {
                        margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 },
                    },
                },
                children: [
                    new Paragraph({ spacing: { before: 1800 } }),
                    pTitle('SISTEMA DE INVENTARIO WEB'),
                    pSubtitle('Manual de Usuario Oficial e Instructivo Operativo'),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 300, after: 300 },
                        children: [
                            new TextRun({
                                text: 'Administración de Productos, Categorías, Control de Stock en Tiempo Real, Entradas, Salidas, Movimientos y Reportes',
                                size: 24,
                                color: COLOR_SECONDARY,
                                italics: true,
                            }),
                        ],
                    }),
                    new Paragraph({ spacing: { before: 2400 } }),
                    pCallout(
                        'Información del Documento',
                        'Plataforma: Laravel 12 · Bootstrap 5 · MySQL 8\nVersión del Sistema: 2026\nSeguridad: Roles, Permisos RBAC, Hash bcrypt y Verificación de Correo',
                        COLOR_PRIMARY,
                        'f0f4f8'
                    ),
                    new Paragraph({ spacing: { before: 1200 } }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        children: [
                            new TextRun({ text: 'Noviembre 2026 · Documentación Oficial', size: 20, color: COLOR_MUTED, font: 'Calibri' }),
                        ],
                    }),
                    new Paragraph({ children: [new PageBreak()] }),

                    // ==================== SECCIÓN 1 ====================
                    pHeading1('1. Introducción y Arquitectura General'),
                    pBody('El Sistema de Inventario Web es una solución empresarial completa diseñada para optimizar y auditar el control de existencias, simplificar el abastecimiento de mercadería y garantizar la integridad contable en cada despacho.'),
                    pHeading2('Características Principales'),
                    pBullet('Control de Stock en Tiempo Real:', 'El ingreso de mercadería (Entradas) incrementa de inmediato el stock disponible, mientras que los despachos (Salidas) lo descuentan de forma automática.'),
                    pBullet('Protección Antidesabastecimiento (RF-08):', 'El sistema bloquea automáticamente cualquier intento de registrar una salida que supere las unidades físicas disponibles en almacén.'),
                    pBullet('Trazabilidad y Auditoría (RF-10):', 'Cada transacción registra con precisión milimétrica la fecha, hora, tipo de movimiento, producto, cantidad, usuario responsable y observación.'),
                    pBullet('Integridad Referencial Garantizada:', 'No es posible eliminar categorías con productos asociados ni productos con historial de movimientos contables.'),
                    pBullet('Diseño Adaptable (Responsive):', 'La interfaz se adapta a pantallas de escritorio, portátiles, tablets y teléfonos móviles con menú colapsable.'),
                    pBullet('Reportes e Impresión Integrada:', 'Generación de vistas valorizadas de inventario optimizadas para impresión directa o guardado en formato PDF.'),

                    // ==================== SECCIÓN 2 ====================
                    new Paragraph({ children: [new PageBreak()] }),
                    pHeading1('2. Roles del Sistema y Matriz de Acceso'),
                    pBody('El sistema implementa control de acceso basado en roles (RBAC) para garantizar que cada colaborador cuente con los privilegios adecuados según sus responsabilidades en la organización.'),
                    pBullet('Administrador (admin):', 'Posee acceso total a todos los módulos: configuración general, catálogo completo, gestión de usuarios, roles, contraseñas, auditoría e informes.'),
                    pBullet('Encargado de almacén (almacen):', 'Perfil operativo para la gestión diaria del inventario: registra y edita productos, categorías, entradas y salidas. No tiene acceso a administración de usuarios ni borrado de productos.'),
                    pBullet('Consultor (consultor):', 'Perfil de consulta y supervisión en modo solo lectura: visualiza el dashboard, catálogo de productos, existencias, historial de movimientos e informes imprimibles.'),

                    pHeading2('Tabla de Privilegios por Rol'),
                    buildPrivilegesTable(),

                    // ==================== SECCIÓN 3 ====================
                    new Paragraph({ children: [new PageBreak()] }),
                    pHeading1('3. Acceso al Sistema (Autenticación)'),
                    pBody('El ingreso a la plataforma se realiza a través de un portal de acceso seguro protegido contra intentos maliciosos:'),
                    ...imageFigure('01-login.png', 'Figura 1: Pantalla de inicio de sesión con opciones de recuperación y autoregistro'),

                    pHeading2('3.1. Instrucciones para Iniciar Sesión'),
                    pBullet('Paso 1:', 'Abra el navegador e ingrese la URL del sistema (por ejemplo, http://127.0.0.1:8000).'),
                    pBullet('Paso 2:', 'Escriba su Nombre de usuario (ej. admin) o su Correo electrónico institucional.'),
                    pBullet('Paso 3:', 'Ingrese su Contraseña secreta.'),
                    pBullet('Paso 4 (Opcional):', 'Active la casilla "Recordar mi sesión" si utiliza un equipo personal seguro.'),
                    pBullet('Paso 5:', 'Presione el botón "Ingresar".'),

                    pHeading2('3.2. Credenciales Iniciales de Prueba'),
                    pCallout(
                        'Usuarios Predeterminados para Pruebas',
                        '• Administrador: admin / admin123  (admin@inventario.test)\n• Encargado de almacén: almacen / almacen123  (almacen@inventario.test)\n• Consultor: consultor / consultor123  (consultor@inventario.test)',
                        COLOR_PRIMARY,
                        'f8f9fa'
                    ),

                    pHeading2('3.3. Verificación de Correo y Recuperación'),
                    pBody('Si olvidó su contraseña, haga clic en "¿Olvidaste tu contraseña?" para recibir un enlace de restablecimiento firmado en su buzón de correo. Si su cuenta no ha verificado su email, el sistema mostrará un banner superior con la opción de reenvío automático.'),

                    // ==================== SECCIÓN 4 ====================
                    new Paragraph({ children: [new PageBreak()] }),
                    pHeading1('4. Panel General (Dashboard)'),
                    pBody('El Dashboard es la central de mando del sistema. Consolida en tiempo real los indicadores estratégicos de existencia y flujo de mercadería:'),
                    ...imageFigure('02-dashboard.png', 'Figura 2: Dashboard principal con tarjetas estadísticas, stock crítico y últimos movimientos'),

                    pHeading2('Componentes del Dashboard'),
                    pBullet('Total de productos:', 'Muestra el número total de referencias y códigos activos en el inventario.'),
                    pBullet('Total de categorías:', 'Número de familias o clasificaciones existentes.'),
                    pBullet('Productos con stock bajo:', 'Indicador de alerta en color rojo que destaca los artículos que han alcanzado o perforado su umbral de seguridad (stock actual <= stock mínimo).'),
                    pBullet('Total de entradas y salidas:', 'Conteo histórico de transacciones de ingreso y despacho registradas.'),
                    pBullet('Tabla de Stock Bajo:', 'Lista inmediata con los productos urgentes de compra o reposición.'),

                    pHeading2('Vista del Dashboard para el Rol Consultor'),
                    pBody('Para el usuario con rol Consultor, el menú lateral se simplifica automáticamente mostrando únicamente los módulos autorizados:'),
                    ...imageFigure('18-dashboard-consultor.png', 'Figura 3: Dashboard adaptado para el perfil de Consultor'),

                    // ==================== SECCIÓN 5 ====================
                    new Paragraph({ children: [new PageBreak()] }),
                    pHeading1('5. Módulo de Productos'),
                    pBody('El catálogo de productos centraliza la información comercial, técnica y operativa de los bienes custodiados en el almacén:'),
                    ...imageFigure('03-productos-listado.png', 'Figura 4: Catálogo de productos con filtros de búsqueda por código, categoría y estado'),

                    pHeading2('5.1. Búsqueda y Filtrado Dinámico'),
                    pBody('En la barra superior puede buscar por código o nombre del producto, filtrar por categoría y seleccionar el estado (Activo / Inactivo) con un clic.'),

                    pHeading2('5.2. Modo Solo Lectura (Consultor)'),
                    pBody('Cuando un usuario con rol Consultor accede a productos, los botones de creación, edición y borrado quedan ocultos:'),
                    ...imageFigure('19-productos-consultor-solo-lectura.png', 'Figura 5: Vista de productos para el rol Consultor sin botones de edición'),

                    pHeading2('5.3. Registrar un Nuevo Producto'),
                    pBody('Para dar de alta un producto, presione el botón azul "Registrar producto" y complete los campos obligatorios:'),
                    ...imageFigure('04-productos-registrar.png', 'Figura 6: Formulario para registrar un nuevo producto con stock y umbrales mínimos'),

                    pBullet('Código:', 'Código de barras o identificador interno único (ej. PROD-001).'),
                    pBullet('Nombre:', 'Denominación descriptiva del producto.'),
                    pBullet('Categoría:', 'Familia de productos a la que pertenece.'),
                    pBullet('Precio:', 'Precio unitario de referencia en moneda local.'),
                    pBullet('Stock inicial:', 'Cantidad de unidades disponibles al momento del alta.'),
                    pBullet('Stock mínimo:', 'Umbral que dispara la alerta roja de stock bajo.'),

                    pHeading2('5.4. Editar un Producto'),
                    pBody('Haga clic en el botón de lápiz azul para modificar cualquier dato del producto:'),
                    ...imageFigure('05-productos-editar.png', 'Figura 7: Formulario de edición de producto'),

                    pCallout(
                        'Regla de Integridad de Productos',
                        'Para proteger los balances contables, los productos que tengan movimientos históricos de entrada o salida no pueden ser eliminados físicamente de la base de datos. Si un producto deja de comercializarse, cámbielo a estado "Inactivo".',
                        COLOR_DANGER,
                        'fdf2f2'
                    ),

                    // ==================== SECCIÓN 6 ====================
                    new Paragraph({ children: [new PageBreak()] }),
                    pHeading1('6. Módulo de Categorías'),
                    pBody('Las categorías permiten estructurar y segmentar el inventario en grupos lógicos para análisis de compras y reportes financieros:'),
                    ...imageFigure('06-categorias.png', 'Figura 8: Listado de categorías con conteo de productos asignados'),

                    pHeading2('6.1. Registrar y Editar Categorías'),
                    pBody('El formulario de categoría solicita el nombre (que debe ser único en la empresa) y una descripción complementaria:'),
                    ...imageFigure('07-categorias-registrar.png', 'Figura 9: Formulario para registrar una categoría'),
                    ...imageFigure('08-categorias-editar.png', 'Figura 10: Formulario de edición de categoría'),

                    pCallout(
                        'Regla de Integridad de Categorías',
                        'No es posible eliminar una categoría que contenga productos asignados. Primero debe reasignar dichos productos a otra categoría o desactivarlos.',
                        COLOR_WARNING,
                        'fffdf0'
                    ),

                    // ==================== SECCIÓN 7 ====================
                    new Paragraph({ children: [new PageBreak()] }),
                    pHeading1('7. Módulo de Entradas (Ingresos al Almacén)'),
                    pBody('Cada ingreso de mercadería debe quedar documentado en el módulo de Entradas para incrementar el stock de forma transparente:'),
                    ...imageFigure('09-entradas.png', 'Figura 11: Historial de entradas con fecha, producto, cantidad y responsable'),

                    pHeading2('7.1. Registrar una Entrada'),
                    pBody('1. Presione el botón verde "Registrar entrada".\n2. Elija el producto en la lista desplegable (se muestra su stock actual).\n3. Ingrese la cantidad que ingresa (entero positivo >= 1).\n4. Especifique la fecha del movimiento y una observación (ej. Factura F-205).\n5. Presione "Guardar entrada".'),
                    ...imageFigure('10-entradas-registrar.png', 'Figura 12: Formulario para registrar ingreso de mercadería al inventario'),

                    pCallout(
                        'Cálculo Automático de Stock (RF-07)',
                        'Al presionar Guardar, el sistema incrementa de inmediato el stock del producto en la base de datos y genera el asiento correspondiente en el historial de movimientos.',
                        COLOR_SUCCESS,
                        'f2f9f4'
                    ),

                    // ==================== SECCIÓN 8 ====================
                    new Paragraph({ children: [new PageBreak()] }),
                    pHeading1('8. Módulo de Salidas (Despachos y Egresos)'),
                    pBody('El módulo de Salidas gestiona la entrega de mercadería para ventas, transferencias o consumo interno:'),
                    ...imageFigure('11-salidas.png', 'Figura 13: Historial de salidas registradas'),

                    pHeading2('8.1. Regla de Validación Estricta (RF-08)'),
                    pBody('El sistema implementa una regla inviolable de bloqueo de sobregiros de stock: si un artículo tiene 5 unidades en existencia y se intenta retirar 6 o más, la transacción es cancelada informando el stock exacto disponible:'),
                    ...imageFigure('12-salidas-registrar.png', 'Figura 14: Registro de salida con indicación del stock disponible entre paréntesis'),

                    pCallout(
                        'Bloqueo Antidesabastecimiento en Base de Datos',
                        'La validación de cantidad se ejecuta dentro de una transacción con bloqueo de fila (DB lock), garantizando que incluso bajo solicitudes simultáneas nunca se produzcan existencias negativas.',
                        COLOR_DANGER,
                        'fdf2f2'
                    ),

                    // ==================== SECCIÓN 9 ====================
                    new Paragraph({ children: [new PageBreak()] }),
                    pHeading1('9. Módulo de Historial de Movimientos'),
                    pBody('El módulo de Movimientos constituye la bitácora de auditoría inmutable del inventario:'),
                    ...imageFigure('13-movimientos.png', 'Figura 15: Historial consolidado de movimientos con filtros por tipo y producto'),

                    pHeading2('9.1. Filtros de Auditoría'),
                    pBullet('Tipo de movimiento:', 'Permite filtrar únicamente las Entradas (ingresos) o Salidas (despachos).'),
                    pBullet('Producto:', 'Muestra el historial completo de un producto en particular.'),
                    pBullet('Vista Consultor:', 'El perfil de supervisión dispone de la misma información de auditoría para fines de control interno:'),
                    ...imageFigure('20-movimientos-consultor.png', 'Figura 16: Historial de movimientos consultado desde el rol Consultor'),

                    // ==================== SECCIÓN 10 ====================
                    new Paragraph({ children: [new PageBreak()] }),
                    pHeading1('10. Módulo de Reportes e Impresión'),
                    pBody('El módulo de Reportes proporciona resúmenes gerenciales valorizados sobre la composición del inventario:'),
                    ...imageFigure('14-reportes.png', 'Figura 17: Módulo de reportes con indicadores clave y botón de impresión directa'),

                    pHeading2('10.1. Indicadores Valorizados'),
                    pBullet('Valor Total del Inventario:', 'Calcula la sumatoria económica exacta de multiplicar el stock disponible de cada producto por su precio unitario.'),
                    pBullet('Total de Unidades Físicas:', 'Total de piezas o artículos en existencia.'),
                    pBullet('Inventario Crítico:', 'Productos que requieren atención urgente de compras.'),

                    pHeading2('10.2. Exportación a PDF e Impresión en Papel'),
                    pBody('Presione el botón "Imprimir reporte" en la esquina superior derecha. El sistema activará el estilo de impresión limpia (@media print), ocultando automáticamente la barra de navegación, el menú lateral y los botones, permitiendo generar un documento PDF perfecto o imprimir en hoja A4.'),

                    // ==================== SECCIÓN 11 ====================
                    new Paragraph({ children: [new PageBreak()] }),
                    pHeading1('11. Administración de Usuarios y Mi Perfil'),
                    pBody('El módulo de usuarios permite al Administrador controlar el personal habilitado para operar el sistema:'),
                    ...imageFigure('15-usuarios.png', 'Figura 18: Listado de usuarios con roles y credenciales asignadas'),

                    pHeading2('11.1. Crear y Editar Usuarios'),
                    pBody('Al crear un usuario se definen sus nombres, correo institucional, nombre de usuario y su rol de acceso:'),
                    ...imageFigure('16-usuarios-registrar.png', 'Figura 19: Formulario para registrar un nuevo usuario con rol'),
                    ...imageFigure('17-usuarios-editar.png', 'Figura 20: Formulario de edición de usuario y reasignación de rol'),

                    pHeading2('11.2. Mi Perfil y Seguridad Personal'),
                    pBody('Cualquier usuario autenticado puede hacer clic en su nombre en la esquina superior derecha para ver su perfil personal, editar sus nombres o actualizar su contraseña secreta.'),

                    // ==================== SECCIÓN 12 ====================
                    new Paragraph({ children: [new PageBreak()] }),
                    pHeading1('12. Preguntas Frecuentes y Solución de Problemas'),
                    pHeading2('¿Por qué el sistema no me permite registrar una salida de mercadería?'),
                    pBody('El sistema verifica que la cantidad solicitada no supere el stock en bodega. Si solicita una cantidad mayor al valor que aparece entre paréntesis, el registro será rechazado (RF-08).'),

                    pHeading2('¿Por qué no puedo eliminar un producto o una categoría?'),
                    pBody('Por razones de auditoría y consistencia contable, no se permite borrar categorías con productos ni productos con entradas/salidas previas. Para retirarlo de las operaciones comerciales, edite el producto y elija el estado "Inactivo".'),

                    pHeading2('¿Cómo obtengo un informe en formato PDF?'),
                    pBody('Ingrese a Reportes, presione el botón "Imprimir reporte" y en la ventana de impresión seleccione "Guardar como PDF" como impresora de destino.'),

                    new Paragraph({ spacing: { before: 400 } }),
                    pCallout(
                        'Soporte Técnico y Mantenimiento',
                        'Para solicitar ampliación de permisos o reportar incidencias en el almacén, comuníquese con el Administrador del sistema a través de admin@inventario.test.',
                        COLOR_PRIMARY,
                        'f0f4f8'
                    ),
                ],
            },
        ],
    });

    const buffer = await Packer.toBuffer(doc);
    fs.writeFileSync(OUTPUT_PATH, buffer);
    console.log(`Documento Word generado exitosamente: ${OUTPUT_PATH} (${Math.round(buffer.length / 1024)} KB)`);
}

generateManual().catch((err) => {
    console.error('Error al generar el manual Word:', err);
    process.exit(1);
});

