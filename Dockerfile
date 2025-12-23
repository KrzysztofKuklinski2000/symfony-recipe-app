FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
  libzip-dev \
  unzip \
  git \ 
  libicu-dev \
  && docker-php-ext-install \
  pdo \
  pdo_mysql \
  zip \ 
  intl 

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# === POCZĄTEK KONFIGURACJI APACHE ===

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/symfony/public|' /etc/apache2/sites-available/000-default.conf


RUN sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/symfony/public>|' /etc/apache2/sites-available/000-default.conf

# 3. Włącz obsługę .htaccess 
RUN sed -i '/<Directory \/var\/www\/html\/symfony\/public>/, /<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/sites-available/000-default.conf


RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html
RUN sed -i 's/User www-data/User root/' /etc/apache2/apache2.conf
RUN sed -i 's/Group www-data/Group root/' /etc/apache2/apache2.conf

EXPOSE 80