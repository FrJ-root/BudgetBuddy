FROM php:8.2-apache
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    zip \
    && docker-php-ext-install pdo pdo_pgsql
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY ./apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite