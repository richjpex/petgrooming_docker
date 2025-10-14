FROM php:8.2-apache

RUN apt-get update && \
    apt-get install -y libzip-dev libpng-dev libonig-dev libxml2-dev libcurl4-openssl-dev && \
    docker-php-ext-install mysqli pdo pdo_mysql gd zip mbstring && \
    rm -rf /var/lib/apt/lists/*

# Enable crucial Apache modules for HTTPS, redirects, and PHP handling
RUN a2enmod ssl \
    && a2enmod rewrite \
    && a2enmod proxy \
    && a2enmod proxy_fcgi

# Remove the default Apache site configuration to prevent conflicts
RUN rm /etc/apache2/sites-enabled/000-default.conf

EXPOSE 80 443

CMD ["apache2-foreground"]