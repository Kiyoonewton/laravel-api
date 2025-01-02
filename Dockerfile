FROM php:8.1-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for layer caching
COPY composer.json composer.lock ./

# Install system dependencies and clear cache in one step to minimize layers
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    libzip-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    librdkafka-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (grouped for layer optimization)
RUN docker-php-ext-install pdo_mysql zip exif pcntl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Install MongoDB extension via PECL and enable it
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

RUN echo "memory_limit = 2048M" >> /usr/local/etc/php/conf.d/custom-memory-limit.ini

# Install rdkafka extension via PECL and enable it
RUN pecl install rdkafka \
    && docker-php-ext-enable rdkafka

# Install Composer and use it to install PHP dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Configure Git to trust the repository
RUN git config --global --add safe.directory /var/www/html

# Copy application source code to the container
COPY . .

# Create necessary directories with proper permissions
RUN mkdir -p bootstrap/cache storage/framework/sessions storage/framework/cache storage/framework/views \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Update Apache configuration to point to the public directory
RUN sed -i 's/\/var\/www\/html/\/var\/www\/html\/public/g' /etc/apache2/sites-available/000-default.conf

# Tune Apache worker settings for high traffic environments
RUN echo "ServerLimit 500" >> /etc/apache2/apache2.conf && \
    echo "MaxRequestWorkers 500" >> /etc/apache2/apache2.conf && \
    echo "StartServers 100" >> /etc/apache2/apache2.conf && \
    echo "MinSpareServers 50" >> /etc/apache2/apache2.conf && \
    echo "MaxSpareServers 200" >> /etc/apache2/apache2.conf

# Expose the default HTTP port
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
