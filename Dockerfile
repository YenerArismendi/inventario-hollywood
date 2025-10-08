# Usa PHP 8.2 con extensiones necesarias
FROM dunglas/frankenphp:php8.2

# Instala dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install intl bcmath zip \
    && rm -rf /var/lib/apt/lists/*

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia archivos del proyecto
WORKDIR /app
COPY . .

# Instala dependencias de PHP
RUN composer install --optimize-autoloader --no-dev

# Compila assets de Node (si usas Vite o npm run build)
RUN npm install && npm run build

# Genera cachés de Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Expone el puerto de aplicación (FrankenPHP usa 8000)
EXPOSE 8000

# Comando de inicio
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
