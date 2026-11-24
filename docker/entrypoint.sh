#!/bin/sh
set -e

echo "=========================================================="
echo "🚀 Iniciando Sistema de Inventario Web en Docker"
echo "=========================================================="

# Esperar a que MariaDB esté completamente disponible
echo "⏳ Verificando disponibilidad de la base de datos MariaDB..."
until mariadb --ssl=0 -h db -u "${DB_USERNAME:-inventario_user}" -p"${DB_PASSWORD:-inventario_pass}" "${DB_DATABASE:-inventario}" -e "SELECT 1;" > /dev/null 2>&1; do
    echo "   Esperando que MariaDB acepte conexiones..."
    sleep 2
done
echo "✅ Conexión con MariaDB exitosa."

# Preparar archivo .env si no existe
if [ ! -f /var/www/.env ]; then
    echo "📝 Generando archivo de configuración .env para Docker..."
    if [ -f /var/www/.env.docker ]; then
        cp /var/www/.env.docker /var/www/.env
    else
        cp /var/www/.env.example /var/www/.env
    fi
fi

# Generar clave de aplicación si no está presente
if ! grep -q "APP_KEY=base64:" /var/www/.env; then
    echo "🔑 Generando clave criptográfica de la aplicación (APP_KEY)..."
    php artisan key:generate --force
fi

# Ajustar permisos de storage y bootstrap/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Ejecutar migraciones de la base de datos
echo "📦 Ejecutando migraciones de base de datos..."
php artisan migrate --force

# Ejecutar seeders iniciales si la tabla roles está vacía
ROLES_COUNT=$(mariadb --ssl=0 -h db -u "${DB_USERNAME:-inventario_user}" -p"${DB_PASSWORD:-inventario_pass}" "${DB_DATABASE:-inventario}" -sse "SELECT COUNT(*) FROM roles;" 2>/dev/null || echo "0")
if [ "$ROLES_COUNT" = "0" ]; then
    echo "🌱 Poblando base de datos con usuarios y catálogo inicial..."
    php artisan db:seed --force
else
    echo "ℹ️ Base de datos ya cuenta con datos iniciales ($ROLES_COUNT roles detectados)."
fi

# Optimización y limpieza de cachés
echo "⚡ Optimizando configuración de Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Iniciar servidor WebSocket (Laravel Reverb) en segundo plano
echo "📡 Iniciando servidor WebSocket (Laravel Reverb) en puerto 8080..."
(
    while true; do
        php artisan reverb:start --host=0.0.0.0 --port=8080 >> /var/log/reverb.log 2>&1
        sleep 2
    done
) &

echo "=========================================================="
echo "✅ Sistema de Inventario listo en http://localhost:8000"
echo "   phpMyAdmin disponible en http://localhost:8081"
echo "   Servidor WebSocket (Reverb) disponible en el puerto 8080"
echo "=========================================================="

# Ejecutar el comando principal del contenedor (php-fpm)
exec "$@"
