# Gunakan base image resmi PHP dengan Apache
FROM php:8.1-apache

# Install ekstensi PHP yang dibutuhkan (mysqli untuk database)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Install dependensi sistem untuk Composer dan AWS SDK (seperti zip)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy file composer.json dan install dependensi
COPY composer.json .
RUN composer install --no-scripts --no-autoloader

# Copy sisa file aplikasi ke dalam container
COPY . .

# Jalankan composer dump-autoload untuk generate file autoload
RUN composer dump-autoload --optimize

# Atur kepemilikan agar Apache bisa menulis (jika ada upload di dalam container)
RUN chown -R www-data:www-data /var/www/html