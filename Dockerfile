FROM php:8.2.4

RUN docker-php-ext-install pdo pdo_mysql sockets
# Instalar dependencias de compilación
RUN apt-get update && apt-get install -y netcat-openbsd -y libpng-dev libjpeg-dev libfreetype6-dev && rm -rf /var/lib/apt/lists/*

# Instalar la extensión GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd

RUN docker-php-ext-install gd
RUN curl -sS https://getcomposer.org/installer | php -- \
     --install-dir=/usr/local/bin --filename=composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .
RUN composer install
