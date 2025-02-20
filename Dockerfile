FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install zip pdo pdo_mysql

RUN a2enmod rewrite


RUN echo "display_errors=1\n" >> /usr/local/etc/php/conf.d/docker-php-ext-error.ini && \
    echo "display_startup_errors=1\n" >> /usr/local/etc/php/conf.d/docker-php-ext-error.ini && \
    echo "error_reporting=E_ALL\n" >> /usr/local/etc/php/conf.d/docker-php-ext-error.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN echo "APP_ENV=prod" > .env && \
    echo "APP_DEBUG=1" >> .env

RUN mkdir -p var && \
    chmod 777 var

RUN composer install --no-dev --no-scripts

RUN npm install
RUN npm run build


RUN php bin/console doctrine:migrations:migrate --no-interaction

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf


RUN chown -R www-data:www-data var/ public/