# Sistema de Inventario Web

Proyecto académico de la Unidad Didáctica **Innovación Tecnología**.
Sistema web para administrar un inventario de productos, controlar entradas y salidas,
consultar el stock y gestionar usuarios mediante roles y permisos.

**Stack:** Laravel 12 · PHP 8.2 · MySQL · Blade · HTML · CSS · JavaScript · Bootstrap 5

---

## 1. Requisitos

- PHP >= 8.2 con extensiones: `pdo_mysql`, `mbstring`, `openssl`
- Composer
- MySQL 8 (o MariaDB)
- Navegador moderno (Chrome, Firefox, Edge, Safari)

## 2. Instalación

```bash
composer install
cp .env.example .env       # si aún no existe .env
php artisan key:generate
```

### 3. Base de datos (MySQL)

Cree la base de datos en MySQL:

```sql
CREATE DATABASE inventario CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Verifique que en `.env` estén estos valores:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventario
DB_USERNAME=root
DB_PASSWORD=********
```

Luego ejecute las migraciones y los datos de prueba:

```bash
php artisan migrate --seed
```

> Para reiniciar los datos de prueba en cualquier momento:
> `php artisan migrate:fresh --seed`
>
> **¿No tiene MySQL a la mano?** Puede probar el sistema con SQLite cambiando en `.env`
> `DB_CONNECTION=mysql` por `DB_CONNECTION=sqlite` y eliminando las líneas `DB_HOST`,
> `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.

## 4. Ejecutar el sistema

```bash
php artisan serve
```

Abra <http://127.0.0.1:8000> e inicie sesión.

## 5. Usuarios de prueba

| Rol                  | Usuario      | Contraseña   | Correo                     |
| -------------------- | ------------ | ------------ | -------------------------- |
| Administrador        | `admin`      | `admin123`   | admin@inventario.test      |
| Encargado de almacén | `almacen`    | `almacen123` | almacen@inventario.test    |
| Consultor            | `consultor`  | `consultor123` | consultor@inventario.test |

También puede iniciar sesión escribiendo el correo electrónico en lugar del usuario.

---

## 6. Módulos

1. **Inicio de sesión** — login con usuario/correo y contraseña (hash), cierre de sesión y protección de rutas.
2. **Dashboard** — tarjetas con total de productos, categorías, productos con stock bajo, entradas y salidas.
3. **Productos** — CRUD completo, búsqueda, filtros por categoría y estado.
4. **Categorías** — CRUD completo.
5. **Entradas** — registra el ingreso de productos y **aumenta el stock automáticamente**.
6. **Salidas** — registra la salida y **disminuye el stock**; no permite salir más del stock disponible.
7. **Movimientos** — historial de entradas y salidas con producto, cantidad, fecha y usuario.
8. **Usuarios** — CRUD de usuarios con asignación de rol.
9. **Reportes** — inventario general, stock bajo, resumen por categoría y de movimientos (imprimible).

> **Integridad de datos:** no se puede eliminar una categoría con productos ni un producto
> con movimientos registrados; el sistema muestra un mensaje de error y conserva la información.

## 7. Roles y permisos

| Permisos                         | Administrador | Encargado | Consultor |
| -------------------------------- | :-----------: | :-------: | :-------: |
| Ver dashboard                    | ✔ | ✔ | ✔ |
| Gestionar usuarios               | ✔ | ✖ | ✖ |
| Ver productos / stock            | ✔ | ✔ | ✔ |
| Crear y editar productos         | ✔ | ✔ | ✖ |
| Eliminar productos               | ✔ | ✖ | ✖ |
| Gestionar categorías             | ✔ | ✔ | ✖ |
| Registrar entradas / salidas     | ✔ | ✔ | ✖ |
| Consultar movimientos            | ✔ | ✔ | ✔ |
| Ver reportes                     | ✔ | ✔ | ✔ |

Los permisos se definen en `config/permissions.php`, se aplican con el middleware
`permission:...` (`app/Http/Middleware/CheckPermission.php`) y se reflejan en la
interfaz con las directivas `@can` de Blade.

---

## 8. Requerimientos funcionales

| RF   | Descripción                                        | Dónde está                                  |
| ---- | -------------------------------------------------- | ------------------------------------------- |
| RF-01 | Iniciar sesión                                    | `LoginController`, `resources/views/auth`    |
| RF-02 | Gestionar usuarios                                | `UserController`                             |
| RF-03 | Gestionar productos                               | `ProductController`                          |
| RF-04 | Gestionar categorías                              | `CategoryController`                         |
| RF-05 | Registrar entradas                                | `EntradaController`                          |
| RF-06 | Registrar salidas                                 | `SalidaController`                           |
| RF-07 | Actualizar stock automáticamente                 | `EntradaController@store`, `SalidaController@store` |
| RF-08 | Impedir salidas superiores al stock               | `StoreExitRequest` + transacción con bloqueo |
| RF-09 | Mostrar productos con stock bajo                  | Dashboard y `ReportController`               |
| RF-10 | Historial de movimientos                          | `MovementController`                         |
| RF-11 | Consultar inventario                              | `ProductController@index`, `ReportController` |
| RF-12 | Dashboard con información resumida                | `DashboardController`                        |

## 9. Requerimientos no funcionales

- **RNF-01** Laravel · **RNF-02** MySQL · **RNF-03** contraseñas con hash (`hashed`)
- **RNF-04** autenticación + roles/permisos · **RNF-05** interfaz sencilla con Bootstrap
- **RNF-06** validaciones en *Form Requests* · **RNF-07** integridad (claves foráneas con `restrict`)
- **RNF-08** funciona en navegadores modernos · **RNF-09** diseño responsive (sidebar adaptable a móvil)
- **RNF-10** arquitectura MVC · **RNF-11** alertas de éxito y error · **RNF-12** código organizado por módulos

## 10. Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/       # Login, Dashboard, Productos, Categorías, Entradas, Salidas, Movimientos, Usuarios, Reportes
│   ├── Middleware/        # CheckPermission (permisos por rol)
│   └── Requests/          # Validaciones (productos, movimientos, salidas)
├── Models/                # User, Role, Category, Product, Movement
config/permissions.php     # Habilidades de cada rol
database/
├── migrations/            # Tablas: roles, users, categories, products, movements
├── seeders/               # Datos de prueba (roles, usuarios, categorías, productos, movimientos)
resources/views/           # Vistas Blade (layouts, módulos)
routes/web.php             # Rutas protegidas por autenticación y permisos
```

## 11. Datos de prueba

- 3 usuarios (uno por rol), 5 categorías, 10 productos (4 con stock bajo) y
  20 movimientos (entradas y salidas) generados por los *seeders*.

## 12. Pruebas automatizadas

```bash
php artisan test
```

**59 pruebas y 254 aserciones** (SQLite en memoria, no toca MySQL):

| Archivo                     | Cobertura                                            |
| --------------------------- | ---------------------------------------------------- |
| `AuthenticationTest`        | RF-01, RNF-03 (login, logout, hash, credenciales)     |
| `UserManagementTest`        | RF-02 (CRUD de usuarios, rol, contraseña propia)      |
| `ProductCrudTest`           | RF-03, RF-11 (CRUD, validaciones, búsqueda, borrado)  |
| `CategoryCrudTest`          | RF-04 (CRUD, nombre único, protección de relaciones)  |
| `StockMovementTest`         | RF-05, RF-06, RF-07, RF-08 (stock y bloqueo de salida) |
| `MovementHistoryTest`       | RF-10 (historial, filtros por tipo y producto)        |
| `DashboardTest`             | RF-09, RF-12 (tarjetas, conteos, stock bajo)          |
| `RolePermissionTest`        | RNF-04 (permisos de admin, encargado y consultor)     |
| `TableLayoutTest`           | Diseño: columnas y alineación de todas las tablas      |

Dos comprobaciones adicionales de diseño:

```bash
php artisan test --filter=TableLayoutTest   # cabeceras vs. celdas de cada tabla
node tools/layout-audit.js                  # render real en Chrome (1440 / 1024 / 390 px)
```

## 13. Diseño de tablas

- Encabezados en mayúsculas con fondo claro y separadores suaves.
- Códigos e identificadores en negrita y números con cifras tabulares (`cell-code`, `cell-num`).
- Columnas numéricas y de fecha centradas, montos alineados a la derecha.
- Códigos de producto y cantidades sin saltos de línea; textos largos con puntos suspensivos.
- Acciones juntas y sin envolver; estados vacíos con icono centrado (`empty-state`).
- En móvil las tablas mantienen su ancho mínimo y se desplazan horizontalmente (`.table-responsive`).

## 14. Capturas de pantalla (para el informe)

Las imágenes están en **`docs/capturas/`** y se regeneran con:

```bash
php artisan serve          # el servidor debe estar corriendo
node tools/take-screenshots.js
node tools/check-pngs.mjs  # valida que las imágenes no estén vacías
```

| Archivo | Módulo |
| ------- | ------ |
| `01-login.png` | Inicio de sesión (RF-01) |
| `02-dashboard.png` | Dashboard con tarjetas (RF-12, RF-09) |
| `03-productos-listado.png` | Productos: listado y búsqueda (RF-03, RF-11) |
| `04-productos-registrar.png` | Productos: formulario de alta |
| `05-productos-editar.png` | Productos: edición |
| `06-categorias.png` | Categorías: listado (RF-04) |
| `07-categorias-registrar.png` | Categorías: alta |
| `08-categorias-editar.png` | Categorías: edición |
| `09-entradas.png` | Entradas: historial (RF-05) |
| `10-entradas-registrar.png` | Entradas: registro (RF-07) |
| `11-salidas.png` | Salidas: historial (RF-06) |
| `12-salidas-registrar.png` | Salidas: registro (RF-08) |
| `13-movimientos.png` | Historial de movimientos (RF-10) |
| `14-reportes.png` | Reportes e inventario general |
| `15-usuarios.png` | Usuarios y roles (RF-02) |
| `16-usuarios-registrar.png` | Usuarios: alta |
| `17-usuarios-editar.png` | Usuarios: edición |
| `18-dashboard-consultor.png` | Rol Consultor: menú reducido (RNF-04) |
| `19-productos-consultor-solo-lectura.png` | Rol Consultor: productos sin botones de edición |
| `20-movimientos-consultor.png` | Rol Consultor: solo consulta |
