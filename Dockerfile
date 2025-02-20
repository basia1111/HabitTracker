FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install zip pdo pdo_mysql

RUN a2enmod rewrite

# Configure PHP sessions correctly (using proper PHP config)
RUN echo "session.cookie_httponly=1" >> /usr/local/etc/php/conf.d/sessions.ini && \
    echo "session.use_only_cookies=1" >> /usr/local/etc/php/conf.d/sessions.ini && \
    echo "session.cookie_secure=1" >> /usr/local/etc/php/conf.d/sessions.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN echo "APP_ENV=prod" > .env

# Create directories and set permissions
RUN mkdir -p var/cache var/sessions && \
    chmod -R 777 var

RUN composer install --no-dev --no-scripts

# Install npm dependencies and build assets
RUN npm install
RUN npm run build

# Create startup script with session directory fix
RUN echo '#!/bin/bash\nphp bin/console doctrine:migrations:migrate --no-interaction\nchmod -R 777 var/sessions\napache2-foreground' > /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

CMD ["/usr/local/bin/startup.sh"]