FROM php:8.2.21-apache AS apache

# set workdir
RUN mkdir -p /var/www/
WORKDIR /var/www

# upgrades!
RUN apt-get update
RUN apt-get -y dist-upgrade
RUN apt-get install -y dos2unix

RUN apt-get install -y nano
RUN apt-get install -y git
RUN apt-get install -y zip unzip libzip-dev
RUN apt-get install -y libxml2-dev
RUN apt-get install -y wget
RUN apt-get install -y sudo
RUN apt-get install -y iputils-ping
RUN apt-get install -y locales locales-all
RUN apt-get install -y libpng-dev
RUN apt-get install -y socat

# install additional PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli soap zip gd

# copy exta php ini files
COPY ./docker/php/* /usr/local/etc/php/conf.d

RUN apt-get clean -y

# set corrent TimeZone
ENV TZ=Europe/Amsterdam
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# install additional webserver packages
RUN a2enmod ssl
RUN a2enmod rewrite
RUN a2enmod headers

# install NodeJS
RUN curl -sL https://deb.nodesource.com/setup_22.x | bash
RUN apt-get install -y nodejs

# copy httpd files
COPY ./docker/httpd.conf /etc/apache2/sites-enabled/000-default.conf

# copy webapp files
COPY ./ /var/www

# copy github token
COPY ./docker/auth.json /root/.composer/auth.json

# install composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# install self signed certifcates to thrust other local dev environments
COPY ./docker/certificates/apache/docker.dev.crt /usr/local/share/ca-certificates
RUN cd /usr/local/share/ca-certificates && update-ca-certificates

# add /var/scripts to $PATH
ENV PATH="/var/scripts:${PATH}"

# entrypoint
COPY ./docker/entrypoint.sh /entrypoint.sh
RUN chmod ugo+x /entrypoint.sh
RUN dos2unix /entrypoint.sh

ENTRYPOINT /entrypoint.sh
