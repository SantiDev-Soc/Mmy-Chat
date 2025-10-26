FROM php:8.2-cli

# 🧱 Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip

# 🧰 Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# 📁 Define directorio de trabajo
WORKDIR /var/www

# 📦 Copia el proyecto Laravel
COPY . .

# ⚙️ Instala dependencias del proyecto
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 🔐 Ajusta permisos
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && docker-php-ext-install pcntl

RUN echo "disable_functions = " >> /usr/local/etc/php/php.ini

RUN pecl install xdebug

# 🐞 Configura Xdebug
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# 🔊 Expone puertos HTTP y WebSocket
EXPOSE 8030
EXPOSE 8081

# 🏁 Comando de inicio: Laravel + Reverb
CMD ["/entrypoint.sh"]
