# Use PHP 8.1 FPM as the base image
FROM php:8.1-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer (Latest Version)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer --version=2.6.4

# Fix permissions for .npm directory
RUN mkdir -p /var/www/.npm && chown -R www-data:www-data /var/www/.npm

# Copy existing application directory contents to the working directory
COPY . /var/www/html

# Set file permissions for the application directory
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Set the user to www-data
USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
