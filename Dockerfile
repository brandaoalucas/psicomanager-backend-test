FROM php:8.2-fpm

# Install system dependencies (alfabeticamente ordenado para SonarQube)
RUN apt-get update && apt-get install -y \
    curl \
    git \
    libfreetype6-dev \
    libicu-dev \
    libjpeg-dev \
    libonig-dev \
    libpng-dev \
    libpq-dev \
    libssl-dev \
    libxml2-dev \
    libzip-dev \
    pkg-config \
    unzip \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    bcmath \
    exif \
    gd \
    intl \
    mbstring \
    pcntl \
    pdo_mysql \
    xml \
    zip

# Install MongoDB extension (versão específica compatível)
RUN pecl install mongodb-1.20.0 \
    && docker-php-ext-enable mongodb

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first (for better caching)
COPY composer.json composer.lock ./

# Install dependencies with platform requirements ignored (temporário)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-req=ext-mongodb

# Copy application files
COPY . /var/www

# Run composer scripts
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Create storage directories
RUN mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && chown -R www-data:www-data /var/www/storage

# Expose port
EXPOSE 8000

# Start PHP built-in server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
