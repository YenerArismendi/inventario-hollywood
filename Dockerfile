# Imagen base de PHP con Apache
FROM php:8.2-apache

# Instalar dependencias necesarias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install intl zip bcmath pdo pdo_mysql

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite

# Configurar el directorio de trabajo
WORKDIR /var/www/html

# Copiar los archivos del proyecto
COPY . .

# Instalar dependencias de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --optimize-autoloader --no-scripts --no-interaction

# Asignar permisos a Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer el puerto 80
EXPOSE 80

# Comando de inicio
CMD ["apache2-foreground"]
