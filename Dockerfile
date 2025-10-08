# ==========================================
# 🐘 Imagen base con PHP 8.3 y servidor FrankenPHP
# ==========================================
FROM dunglas/frankenphp:php8.3

# Instala dependencias del sistema y extensiones necesarias
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libonig-dev libxml2-dev nodejs npm \
    && install-php-extensions intl bcmath zip pdo_mysql mbstring xml gd exif pcntl \
    && rm -rf /var/lib/apt/lists/*

# Instala Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define el directorio de trabajo
WORKDIR /app

# Copia solo los archivos de Composer primero (para aprovechar la caché)
COPY composer.json composer.lock ./

# Instala dependencias PHP (modo producción)
RUN composer install --no-dev --no-interaction --optimize-autoloader --prefer-dist || true

# Copia el resto del proyecto
COPY . .

# Copia el archivo de entorno de producción si existe
RUN if [ -f .env.production ]; then cp .env.production .env; fi

# Instala dependencias frontend (si existen)
RUN if [ -f package.json ]; then npm install && npm run build; fi

# Genera la key y cachés de Laravel (solo si .env existe)
RUN if [ -f .env ]; then \
    php artisan key:generate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache; \
fi

# Ajusta permisos para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Expone el puerto (Railway asigna uno automáticamente)
EXPOSE 8000

# 🚀 Comando de inicio - Usa el puerto dinámico asignado por Railway
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
