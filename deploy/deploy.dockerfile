FROM php:8.2.27-apache AS apache

RUN apt-get update
RUN apt-get --no-install-recommends -y install git curl zip unzip libzip-dev libxml2-dev wget
RUN apt-get clean

RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends \
&& pecl install imagick \
&& docker-php-ext-enable imagick

# install additional PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli soap zip gd

RUN mkdir -p /mnt/build
RUN mkdir -p /mnt/patch_files
RUN mkdir -p /mnt/repo

COPY ./patch_files/.env /mnt/patch_files/.env
COPY ./patch_files/index.php /mnt/patch_files/index.php

# install NodeJS
RUN curl -sL https://deb.nodesource.com/setup_25.x | bash
RUN apt-get install -y nodejs

# install composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

COPY ./entrypoint.sh /
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
