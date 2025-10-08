# Imagen base con PHP 8.2 y servidor FrankenPHP
FROM dunglas/frankenphp:php8.2

# Instala dependencias y extensiones PHP requeridas
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libonig-dev libxml2-dev \
    && install-php-extensions intl bcmath zip pdo pdo_mysql mbstring xml \
    && rm -rf /var/lib/apt/lists/*

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define directorio de trabajo
WORKDIR /app

# Copia archivos del proyecto
COPY . .

# Instala dependencias de PHP
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

# Compila los assets de frontend si los tienes
RUN npm install && npm run build || true

# Genera cachés de Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Abre el puerto
EXPOSE 8000

# Comando de inicio
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
