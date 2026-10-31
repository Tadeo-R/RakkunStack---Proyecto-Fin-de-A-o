FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql fileinfo

COPY Docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY Docker/php/upload.ini /usr/local/etc/php/conf.d/upload.ini
COPY . /var/www/competti

RUN mkdir -p /var/www/competti/Comppeti/html/uploads

RUN chown -R www-data:www-data /var/www/competti/Comppeti/html/uploads