FROM php:8.2-apache-bullseye

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    default-mysql-client \
    unzip \
    git \
    && docker-php-ext-install mysqli pdo_mysql zip gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]