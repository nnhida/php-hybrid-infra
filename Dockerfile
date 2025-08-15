# Gunakan base image AlmaLinux 9 (pengganti CentOS 9 yang stabil)
FROM almalinux:9

# 1. Install Apache (httpd), PHP, ekstensi yang dibutuhkan, dan tools lainnya
# Menggunakan dnf sebagai package manager
RUN dnf update -y && \
    dnf install -y \
        httpd \
        php \
        php-mysqlnd \
        php-zip \
        git \
        unzip && \
    dnf clean all

# 2. Install Composer (caranya tetap sama, karena universal)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Atur working directory ke root direktori web Apache
WORKDIR /var/www/html

# 4. Copy file composer dan install dependensi (AWS SDK)
COPY composer.json .
RUN composer install --no-scripts --no-autoloader --no-dev --working-dir=/var/www/html

# 5. Copy sisa file aplikasi ke dalam container
COPY . .
RUN composer dump-autoload --optimize --working-dir=/var/www/html

# 6. Atur kepemilikan file ke user 'apache' (user default httpd di CentOS)
RUN chown -R apache:apache /var/www/html

# 7. Expose port 80 agar bisa diakses dari luar container
EXPOSE 80

# 8. Perintah untuk menjalankan Apache di foreground saat container dimulai
CMD ["/usr/sbin/httpd", "-D", "FOREGROUND"]
