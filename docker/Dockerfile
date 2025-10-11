# PHP + Apache
FROM php:8.2-apache

# Системные пакеты
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libzip-dev zip unzip \
    libicu-dev git \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install gd pdo_mysql mysqli sockets intl \
 && rm -rf /var/lib/apt/lists/*

# Включаем mod_rewrite и настраиваем vhost на /var/www/public + .htaccess + индекс
RUN a2enmod rewrite && \
    sed -ri 's#DocumentRoot /var/www/html#DocumentRoot /var/www/public#g' /etc/apache2/sites-available/000-default.conf && \
    printf "\n<Directory /var/www/public>\n  AllowOverride All\n  Require all granted\n</Directory>\n" >> /etc/apache2/apache2.conf && \
    echo "DirectoryIndex index.php index.html" > /etc/apache2/conf-available/dirindex.conf && \
    a2enconf dirindex

# Xdebug (режим берётся из ENV XDEBUG_MODE)
RUN pecl install xdebug \
  && docker-php-ext-enable xdebug \
  && { \
       echo "xdebug.mode=\${XDEBUG_MODE}"; \
       echo "xdebug.start_with_request=yes"; \
       echo "xdebug.client_host=host.docker.internal"; \
       echo "xdebug.client_port=9003"; \
       echo "xdebug.log=/tmp/xdebug.log"; \
       echo "xdebug.idekey=PHPSTORM"; \
     } > /usr/local/etc/php/conf.d/xdebug.ini

# Выравниваем uid/gid www-data под локальные 1000:1000 (чтобы не было read-only в IDE)
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Composer (копируем бинарь из официального образа)
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_MEMORY_LIMIT=-1

# Рабочая директория проекта
WORKDIR /var/www

# Права (dev): на весь /var/www
RUN chown -R www-data:www-data /var/www
