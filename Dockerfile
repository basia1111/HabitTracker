FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install zip pdo pdo_mysql

RUN a2enmod rewrite

# Configure Apache to handle PHP sessions correctly
RUN echo 'php_admin_value[session.cookie_httponly] = 1' >> /etc/apache2/conf-enabled/sessions.conf && \
    echo 'php_admin_value[session.use_only_cookies] = 1' >> /etc/apache2/conf-enabled/sessions.conf && \
    echo 'php_admin_value[session.cookie_secure] = 1' >> /etc/apache2/conf-enabled/sessions.conf

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
RUN echo '#!/bin/bash\nphp bin/console doctrine:migrations:migrate --no-interaction\nchmod -R 777 var/sessions\nchmod -R 777 var/cache\napache2-foreground' > /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

CMD ["/usr/local/bin/startup.sh"]