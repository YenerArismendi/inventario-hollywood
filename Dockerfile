# ==========================================
# 🐘 Imagen base con PHP 8.3 y servidor FrankenPHP
# ==========================================
FROM dunglas/frankenphp:php8.3

# Instala dependencias del sistema y extensiones necesarias
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libonig-dev libxml2-dev nodejs npm \
    && install-php-extensions intl bcmath zip pdo pdo_mysql mbstring xml gd exif pcntl \
    && rm -rf /var/lib/apt/lists/*

# Instala Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define el directorio de trabajo
WORKDIR /app

# Copia solo los archivos de Composer primero (para aprovechar la caché)
COPY composer.json composer.lock ./

# Instala dependencias PHP (sin dev, optimizado, silencioso)
RUN composer install --no-dev --no-interaction --optimize-autoloader --prefer-dist

# Copia el resto del proyecto
COPY . .

# Compila los assets de frontend (si existen)
RUN if [ -f package.json ]; then npm install && npm run build; fi

# Genera cachés de Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Establece permisos correctos para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Expone el puerto (Railway usa el 8000 por defecto)
EXPOSE 8000

# Comando de inicio del servidor (FrankenPHP ejecuta PHP nativo)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
