# Despliegue con Docker - Sistema de Inventario Web

Este proyecto cuenta con un entorno Dockerizado completo y listo para producción o desarrollo mediante **Docker Compose**.

---

## 🛠️ Servicios Incluidos

| Servicio | Contenedor | Imagen | Puerto Host | Descripción |
|---|---|---|---|---|
| **App** | `inventario_app` | PHP 8.3 FPM + extensiones | Interno (9000) | Aplicación Laravel 12 |
| **Web** | `inventario_web` | Nginx Alpine | `8000` | Servidor web HTTP y reverse proxy |
| **DB** | `inventario_db` | MariaDB 10.11 | `3307` | Base de datos relacional persistente |
| **PMA** | `inventario_pma` | phpMyAdmin Latest | `8081` | Administrador visual de base de datos |

---

## 🚀 Inicio Rápido

### 1. Construir e Iniciar los Contenedores
```bash
docker compose up -d --build
```

### 2. Verificar Estado de los Servicios
```bash
docker compose ps
```

### 3. Ver Registros en Vivo
```bash
docker compose logs -f
```

---

## 🌐 Enlaces de Acceso

- **Aplicación Web:** [http://localhost:8000](http://localhost:8000)
- **phpMyAdmin:** [http://localhost:8081](http://localhost:8081)
  - **Servidor:** `db`
  - **Usuario:** `inventario_user`
  - **Contraseña:** `inventario_pass`
  - *(O como root con contraseña `root_secure_password`)*

---

## 👤 Credenciales Iniciales del Sistema

| Rol | Usuario / Correo | Contraseña |
|---|---|---|
| **Administrador** | `admin` / `admin@inventario.test` | `admin123` |
| **Encargado Almacén** | `almacen` / `almacen@inventario.test` | `almacen123` |
| **Consultor** | `consultor` / `consultor@inventario.test` | `consultor123` |

---

## 🛑 Detener los Contenedores

```bash
# Detener sin borrar datos
docker compose down

# Detener eliminando volúmenes de base de datos
docker compose down -v
```
