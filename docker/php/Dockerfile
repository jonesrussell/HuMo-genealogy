FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory
COPY . .

# Install dependencies
RUN composer install

# Set permissions for storage and media
RUN chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/media

# Create user with same ID as host (using ARG)
ARG USERID=1000
ARG GROUPID=1000

# Modify www-data user/group
RUN usermod -u ${USERID} www-data \
    && groupmod -g ${GROUPID} www-data \
    && chown -R www-data:www-data /var/www

# Configure PHP-FPM to run as www-data
RUN sed -i "s/user = www-data/user = www-data/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/group = www-data/group = www-data/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/listen.owner = www-data/listen.owner = www-data/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/listen.group = www-data/listen.group = www-data/g" /usr/local/etc/php-fpm.d/www.conf

# Copy PHP config
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Switch to www-data user
USER www-data
