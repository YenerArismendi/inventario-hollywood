# Dockerfile para Laravel

# Usamos una imagen oficial de PHP 8.2 con FPM (FastCGI Process Manager), que es ideal para Nginx/Apache.
FROM php:8.2-fpm

# Argumentos para el usuario y grupo, útil para permisos.
ARG user
ARG uid

# Instalamos dependencias del sistema necesarias para Laravel.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Instalamos las extensiones de PHP que Laravel comúnmente necesita.
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Obtenemos la última versión de Composer, el manejador de paquetes de PHP.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Creamos un usuario para la aplicación para no correrla como root (más seguro).
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Establecemos el directorio de trabajo dentro del contenedor.
WORKDIR /var/www

# Copiamos los archivos de la aplicación a la carpeta de trabajo del contenedor.
COPY . .

# Cambiamos el propietario de los archivos al usuario que creamos.
RUN chown -R $user:$user /var/www

# Exponemos el puerto 9000, que es el puerto por defecto en el que escucha PHP-FPM.
EXPOSE 9000

# El comando que se ejecutará cuando el contenedor inicie.
CMD ["php-fpm"]
