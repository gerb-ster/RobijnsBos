FROM php:8.2.27-apache AS apache

# set workdir
RUN mkdir -p /var/www/
WORKDIR /var/www

# Install dependencies for the operating system software
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    zip \
    unzip \
    libzip-dev \
    curl

# install PHP exts
RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo_mysql

# Copy existing application directory contents to the working directory
COPY ../web-app /var/www

# chown www-data
RUN chown -R www-data:www-data /var/www

# Expose port 9000 and start php-fpm server (for FastCGI Process Manager)
EXPOSE 9000

COPY ./build/entrypoint.sh /entrypoint.sh
RUN chmod ugo+x /entrypoint.sh

ENTRYPOINT /entrypoint.sh
