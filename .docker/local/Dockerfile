# Sử dụng image PHP 8.4 với FPM
FROM php:8.4-fpm

# Cài đặt các dependencies cần thiết
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    default-mysql-client \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài đặt Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Cài đặt Node.js 22
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
RUN apt-get install -y nodejs

# Thiết lập thư mục làm việc
WORKDIR /var/www

# Copy mã nguồn vào container
COPY . .

# Cài đặt các dependencies PHP và Node.js
RUN composer install --no-dev --optimize-autoloader
RUN npm install

# Cấp quyền cho thư mục storage và bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache

# Copy file cấu hình Supervisor
COPY .docker/local/queue/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port cho PHP-FPM và Vite
EXPOSE 9000 5173

# Khởi chạy Supervisor để quản lý nhiều process (PHP-FPM + Vite)
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
