FROM php:8.3-fpm

# Instalar dependencias del sistema operativo
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    mariadb-client \
    netcat-traditional \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP requeridas por Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

# Configuración personalizada de PHP
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir directorio de trabajo
WORKDIR /var/www

# Copiar archivos de dependencias de Composer primero (optimización de caché)
COPY composer.json composer.lock ./

# Instalar dependencias de PHP sin scripts
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copiar todo el código fuente del proyecto
COPY . /var/www

# Regenerar autoload con scripts y optimizaciones
RUN composer dump-autoload --optimize

# Asegurar permisos correctos en directorios de escritura
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Configurar el script de inicio (entrypoint) y convertir saltos de línea Windows CRLF a Linux LF
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
