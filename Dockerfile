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

# Configurar e instalar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql sockets

# Instalar Composer (solo una forma)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /app

# Copiar solo los archivos necesarios para composer install primero
COPY composer.json composer.lock ./

# Instalar dependencias de Composer
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader --no-scripts

# Copiar el resto de los archivos
COPY . .

# Establecer permisos adecuados
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage

# Ejecutar scripts post-instalación si es necesario
RUN composer dump-autoload --optimize
