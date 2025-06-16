FROM php:8.2.4

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    netcat-openbsd \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql sockets pcntl

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copiar solo los archivos necesarios para composer primero
COPY composer.json composer.lock ./

# Instalar dependencias (sin dev para producción)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copiar el resto de la aplicación
COPY . .

# Establecer permisos
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage