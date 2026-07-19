# Use official PHP 8.0 FPM base image
FROM php:8.0-fpm

# Set working directory
WORKDIR /var/web

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev

# Clear apt cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath xml
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Install Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . /var/web

# Set permissions for Laravel storage and bootstrap cache
RUN chown -R www-data:www-data /var/web/storage /var/web/bootstrap/cache
RUN chmod -R 775 /var/web/storage /var/web/bootstrap/cache

# Expose port 9000 and start fpm server
EXPOSE 9000
CMD ["php-fpm"]
