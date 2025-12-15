FROM php:8.2-apache

# Enable Apache rewrite
RUN a2enmod rewrite

# Allow .htaccess to work
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Install system deps + PHP extensions (intl WAJIB)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    libicu-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    intl

# Set document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Cloud Run port
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf \
    /etc/apache2/sites-available/000-default.conf

EXPOSE 8080

WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files first (cache friendly)
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy app source
COPY . .

# Permission CI4
RUN chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

# Start Apache
CMD ["apache2-foreground"]
