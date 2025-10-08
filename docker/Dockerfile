# PHP + Apache
FROM php:8.2-apache

# Системные пакеты (gd, zip и пр.)
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libzip-dev zip unzip \
    libicu-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install gd pdo_mysql mysqli sockets intl \
 && rm -rf /var/lib/apt/lists/*

# mod_rewrite и удобный vhost
RUN a2enmod rewrite
# Обновляем виртуальный хост: позволяем .htaccess и включаем индекс
RUN sed -ri 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html#g' /etc/apache2/sites-available/000-default.conf && \
    printf "\n<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>\n" >> /etc/apache2/apache2.conf

# Xdebug
RUN pecl install xdebug \
  && docker-php-ext-enable xdebug \
  && { \
       echo "xdebug.mode=\${XDEBUG_MODE}" ; \
       echo "xdebug.start_with_request=yes" ; \
       echo "xdebug.client_host=host.docker.internal" ; \
       echo "xdebug.client_port=9003" ; \
       echo "xdebug.log=/tmp/xdebug.log" ; \
       echo "xdebug.idekey=PHPSTORM" ; \
     } > /usr/local/etc/php/conf.d/xdebug.ini

# Полезные права на веб-корень (dev)
RUN chown -R www-data:www-data /var/www/html
