FROM php:8.3-apache
RUN apt-get update && apt-get install -y libonig-dev libxml2-dev unzip && docker-php-ext-install pdo_mysql mbstring dom && rm -rf /var/lib/apt/lists/*
RUN a2enmod rewrite && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
WORKDIR /var/www/html
COPY . .
RUN chown -R www-data:www-data storage bootstrap/cache
