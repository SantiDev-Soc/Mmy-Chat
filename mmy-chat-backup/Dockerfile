FROM php:8.2-cli

# 🧱 Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip exif pcntl gd

# 🧰 Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# 🧪 Instala Node.js y npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# 📁 Define directorio de trabajo
WORKDIR /var/www

# 📦 Copia el proyecto Laravel
COPY . .

# ⚙️ Instala dependencias del backend
RUN composer install --no-dev --optimize-autoloader

# 🎨 Instala y compila frontend con Vite
RUN npm install && npm run build

# 🔐 Ajusta permisos
RUN chown -R www-data:www-data storage bootstrap/cache

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8020

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8020"]


