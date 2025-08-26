FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install zip pdo pdo_mysql

RUN a2enmod rewrite

# Configure PHP sessions correctly
RUN echo "session.cookie_httponly=1" >> /usr/local/etc/php/conf.d/sessions.ini && \
    echo "session.use_only_cookies=1" >> /usr/local/etc/php/conf.d/sessions.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN echo "APP_ENV=prod" > .env

# Create directories
RUN mkdir -p var/cache var/sessions var/log

# Install dependencies
RUN composer install --no-dev --no-scripts

# Install npm dependencies and build assets
RUN npm install
RUN npm run build

# Create startup script with proper permission fixes
RUN echo '#!/bin/bash\n\
php bin/console doctrine:migrations:migrate --no-interaction\n\
# Clear cache as root first\n\
php bin/console cache:clear\n\
# Set proper permissions\n\
chown -R www-data:www-data var\n\
chmod -R 777 var\n\
# Start Apache\n\
apache2-foreground' > /usr/local/bin/startup.sh

RUN chmod +x /usr/local/bin/startup.sh

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

CMD ["/usr/local/bin/startup.sh"]