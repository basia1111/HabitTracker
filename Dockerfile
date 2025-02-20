FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install zip pdo pdo_mysql

RUN a2enmod rewrite

# Configure PHP sessions explicitly
RUN mkdir -p /var/lib/php/sessions && \
    chmod 777 /var/lib/php/sessions && \
    echo "session.save_handler=files" >> /usr/local/etc/php/conf.d/sessions.ini && \
    echo "session.save_path=/var/lib/php/sessions" >> /usr/local/etc/php/conf.d/sessions.ini && \
    echo "session.gc_probability=1" >> /usr/local/etc/php/conf.d/sessions.ini && \
    echo "session.gc_divisor=100" >> /usr/local/etc/php/conf.d/sessions.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Set environment variables
RUN echo "APP_ENV=prod" > .env && \
    echo "APP_DEBUG=1" >> .env

# Create directories and set permissions
RUN mkdir -p var/cache && \
    chmod -R 777 var

# Install dependencies
RUN composer install --no-dev --no-scripts

# Install npm dependencies and build assets
RUN npm install
RUN npm run build

# Create startup script
RUN echo '#!/bin/bash\nphp bin/console doctrine:migrations:migrate --no-interaction\napache2-foreground' > /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Set permissions
RUN chown -R www-data:www-data var/ public/ /var/lib/php/sessions

CMD ["/usr/local/bin/startup.sh"]