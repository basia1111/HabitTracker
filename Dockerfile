
FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) iconv mbstring gd pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

RUN useradd -m -u 1000 dockeruser

USER dockeruser

COPY --chown=dockeruser:dockeruser . /var/www/html

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install

RUN chown -R dockeruser:dockeruser /var/www/html/var /var/www/html/vendor /var/www/html/public
