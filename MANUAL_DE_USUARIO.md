# Manual de Usuario · Sistema de Inventario Web

Bienvenido al **Manual de Usuario del Sistema de Inventario Web**. Esta guía proporciona instrucciones detalladas e ilustradas paso a paso para utilizar todas las funciones del sistema, describiendo los roles, permisos, flujos operativos y políticas de seguridad e integridad de datos.

---

## 1. Introducción y Arquitectura General

El **Sistema de Inventario Web** es una plataforma diseñada para administrar el catálogo de productos, controlar los ingresos y despachos de mercadería en tiempo real, alertar sobre stock crítico y auditar la trazabilidad de cada movimiento con control estricto de roles y permisos.

### Características Principales
- **Control de Stock en Tiempo Real:** Las entradas incrementan el inventario y las salidas lo descuentan automáticamente (RF-05, RF-06, RF-07).
- **Protección Antidesabastecimiento:** Es imposible registrar una salida que supere el stock actual en existencia (RF-08).
- **Integridad Referencial:** Bloqueo de eliminación para categorías con productos y productos con movimientos contables.
- **Seguridad y Auditoría:** Contraseñas protegidas mediante algoritmo Hash (RNF-03), verificación de correo electrónico y registro de usuario responsable en cada transacción.
- **Diseño Adaptable (Responsive):** Funciona en computadoras de escritorio, tablets y dispositivos móviles con menú lateral desplegable.
- **Reportes Imprimibles:** Vista preparada para impresión directa en papel o exportación a PDF.

---

## 2. Roles del Sistema y Matriz de Acceso

El acceso a las opciones del menú y botones de acción se adapta de forma automática al rol asignado a su cuenta:

```mermaid
flowchart TD
    subgraph Roles
        Admin["👑 Administrador"]
        Almacen["📦 Encargado de Almacén"]
        Consultor["👁️ Consultor"]
    end

    subgraph Modulos
        D["Dashboard y Estadísticas"]
        P["Productos y Categorías"]
        M["Entradas, Salidas y Stock"]
        R["Reportes e Impresión"]
        U["Gestión de Usuarios"]
    end

    Admin --> D
    Admin --> P
    Admin --> M
    Admin --> R
    Admin --> U

    Almacen --> D
    Almacen --> P
    Almacen --> M
    Almacen --> R

    Consultor --> D
    Consultor -.->|Solo Lectura| P
    Consultor -.->|Solo Lectura| M
    Consultor --> R
```

### Tabla de Privilegios

| Módulo / Acción | Administrador | Encargado de Almacén | Consultor |
| :--- | :---: | :---: | :---: |
| **Acceso al Dashboard** | ✔ Sí | ✔ Sí | ✔ Sí |
| **Consultar Productos y Categorías** | ✔ Sí | ✔ Sí | ✔ Sí |
| **Crear y Editar Productos** | ✔ Sí | ✔ Sí | ✖ No |
| **Eliminar Productos** | ✔ Sí | ✖ No | ✖ No |
| **Crear y Editar Categorías** | ✔ Sí | ✔ Sí | ✖ No |
| **Eliminar Categorías** | ✔ Sí | ✖ No | ✖ No |
| **Registrar Entradas y Salidas** | ✔ Sí | ✔ Sí | ✖ No |
| **Modificar / Eliminar Entradas y Salidas** | ✔ Sí | ✔ Sí | ✖ No |
| **Consultar Historial de Movimientos** | ✔ Sí | ✔ Sí | ✔ Sí |
| **Ver e Imprimir Reportes** | ✔ Sí | ✔ Sí | ✔ Sí |
| **Gestión de Usuarios (CRUD y Roles)** | ✔ Sí | ✖ No | ✖ No |
| **Editar Mi Propio Perfil y Contraseña** | ✔ Sí | ✔ Sí | ✔ Sí |

---

## 3. Acceso al Sistema (Autenticación)

### 3.1. Inicio de Sesión (Login)
1. Ingrese a la dirección web del sistema (por ejemplo, `http://127.0.0.1:8000`).
2. Introduzca su **Nombre de usuario** o su **Correo electrónico institucional**.
3. Escriba su **Contraseña**.
4. *(Opcional)* Marque la casilla **Recordar mi sesión** para mantenerla activa en su equipo.
5. Presione el botón **Ingresar**.

![Inicio de sesión](docs/capturas/01-login.png)

> **Credenciales de prueba iniciales:**
> - **Administrador:** `admin` / `admin123`
> - **Encargado de almacén:** `almacen` / `almacen123`
> - **Consultor:** `consultor` / `consultor123`

### 3.2. Registro de Nuevos Usuarios
Si su empresa permite el autoregistro, haga clic en **¿No tienes una cuenta? Registrate como nuevo usuario** en la pantalla de bienvenida.

### 3.3. Verificación de Correo y Recuperación
- **Verificación:** Si su correo no está verificado, verá un aviso con el botón de reenvío del correo con enlace firmado.
- **Recuperación:** Puede presionar **¿Olvidaste tu contraseña?** para recibir un token de restablecimiento.

---

## 4. Panel General (Dashboard)

El Dashboard ofrece una panorámica instantánea con indicadores clave de stock y actividad:

![Panel general - Dashboard](docs/capturas/02-dashboard.png)

### Elementos Visuales del Dashboard
1. **Tarjetas de Estadísticas:** Muestran el total de productos, categorías, productos con stock bajo en alerta roja, y acumulados de entradas y salidas.
2. **Productos con Stock Bajo:** Cuadro de alerta con los artículos cuya cantidad disponible está en nivel crítico ($\le \text{stock mínimo}$).
3. **Últimos Movimientos:** Resumen de las transacciones más recientes realizadas en el almacén.

#### Vista del Dashboard para Rol Consultor
El usuario con rol **Consultor** observa un menú lateral restringido a solo consulta sin botones de acción administrativa:

![Dashboard para Rol Consultor](docs/capturas/18-dashboard-consultor.png)

---

## 5. Módulo de Productos

Permite administrar el catálogo de artículos, controlar precios y definir stock mínimo.

### 5.1. Listado y Búsqueda de Productos
En la pantalla de productos encontrará la barra de búsqueda por código/nombre y filtros por categoría y estado:

![Listado de productos](docs/capturas/03-productos-listado.png)

*Para el rol Consultor, la tabla se visualiza en modo de solo lectura sin botones de edición ni eliminación:*

![Productos en modo solo lectura](docs/capturas/19-productos-consultor-solo-lectura.png)

### 5.2. Registrar un Nuevo Producto
Presione el botón azul **Registrar producto** y complete el formulario con los datos solicitados:

![Formulario de alta de producto](docs/capturas/04-productos-registrar.png)

### 5.3. Modificar un Producto
Haga clic en el botón con icono de lápiz (**Editar**) en la fila correspondiente:

![Edición de producto](docs/capturas/05-productos-editar.png)

> **Protección de Integridad:** Un producto con movimientos de entrada o salida no puede ser eliminado. Para darlo de baja sin romper el historial contable, cámbielo a estado `Inactivo`.

---

## 6. Módulo de Categorías

Permite clasificar los productos para agrupar inventarios y generar resúmenes estadísticos.

### 6.1. Listado de Categorías
Visualice el nombre, descripción y cantidad de productos pertenecientes a cada familia:

![Listado de categorías](docs/capturas/06-categorias.png)

### 6.2. Registro y Edición de Categorías
- **Registrar:** Presione **Registrar categoría** e introduzca el nombre único.
- **Editar:** Presione el icono de lápiz para modificar el nombre o descripción.

![Registrar categoría](docs/capturas/07-categorias-registrar.png)

![Editar categoría](docs/capturas/08-categorias-editar.png)

---

## 7. Módulo de Entradas (Ingresos)

Gestiona el abastecimiento del inventario aumentando las existencias de forma inmediata.

### 7.1. Historial de Entradas
Listado ordenado con fecha, producto, cantidad ingresada y usuario responsable:

![Historial de entradas](docs/capturas/09-entradas.png)

### 7.2. Registrar una Entrada
1. Presione **Registrar entrada**.
2. Seleccione el producto, ingrese la cantidad ($\ge 1$), fecha y observación (número de factura o proveedor).
3. Al guardar, el inventario aumentará automáticamente.

![Registro de entrada](docs/capturas/10-entradas-registrar.png)

---

## 8. Módulo de Salidas (Despachos)

Registra los egresos de inventario por ventas o consumo con control estricto antidesabastecimiento.

### 8.1. Historial de Salidas
Muestra los despachos realizados y el motivo registrado:

![Historial de salidas](docs/capturas/11-salidas.png)

### 8.2. Registrar una Salida con Validación de Stock
Seleccione el producto y especifique la cantidad. El sistema muestra entre paréntesis el stock disponible actual y bloquea cualquier intento de retirar más unidades de las existentes.

![Registro de salida con validación](docs/capturas/12-salidas-registrar.png)

---

## 9. Módulo de Historial de Movimientos

Auditoría global de todas las operaciones de entrada y salida realizadas en el sistema:

![Historial de movimientos consolidado](docs/capturas/13-movimientos.png)

*Para el rol Consultor, se visualiza la consulta histórica con filtros de auditoría:*

![Movimientos para Consultor](docs/capturas/20-movimientos-consultor.png)

---

## 10. Módulo de Reportes e Impresión

Permite evaluar el valor económico del inventario, revisar los productos que requieren reposición y preparar informes para gerencia.

![Reportes e inventario valorizado](docs/capturas/14-reportes.png)

> Presione el botón **Imprimir reporte** en la esquina superior para generar automáticamente una versión limpia sin menús, lista para imprimir o guardar en **PDF**.

---

## 11. Administración de Usuarios y Roles

Exclusivo para el rol Administrador, permite gestionar el personal y delegar responsabilidades.

### 11.1. Lista de Usuarios
Visualice los usuarios registrados, sus correos, roles asignados y fecha de registro:

![Administración de usuarios](docs/capturas/15-usuarios.png)

### 11.2. Alta y Edición de Usuarios
Permite asignar roles (`Administrador`, `Encargado de almacén`, `Consultor`) o modificar contraseñas y accesos:

![Registrar usuario](docs/capturas/16-usuarios-registrar.png)

![Editar usuario](docs/capturas/17-usuarios-editar.png)

---

## 12. Preguntas Frecuentes

1. **¿Por qué una salida no me permite guardar?**  
   Porque la cantidad solicitada es superior al stock disponible actual del producto (RF-08).
2. **¿Por qué no puedo eliminar un producto o categoría?**  
   Para proteger la contabilidad del negocio, los productos con movimientos y las categorías con productos no se pueden eliminar. En su lugar, edítelos y colóquelos como `Inactivo`.
3. **¿Cómo exporto un reporte a PDF?**  
   En la sección **Reportes**, haga clic en **Imprimir reporte** y seleccione **Guardar como PDF** en el destino de impresión.

---

*Manual de Usuario ilustrado · Sistema de Inventario Web 2026.*
