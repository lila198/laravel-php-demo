# Using PHP 8.4 FPM base image
FROM php:8.4.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clean up apt cache to reduce image size
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Retrieve the latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html

# Copy project files into the container
COPY . /var/www/html
COPY ./docker/www.conf /usr/local/etc/php-fpm.d/www.conf

# Set permissions for the working directory
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Install project dependencies using Composer
RUN composer install --optimize-autoloader --no-dev

# Expose the port used by PHP-FPM (default: 9000)
EXPOSE 9000

# Start the PHP-FPM service
CMD ["php-fpm"]
