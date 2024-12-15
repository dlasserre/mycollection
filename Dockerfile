# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
FROM mysql:8
# Setup the custom configuration
ADD ./docker/mysql/mysqld.cnf /etc/mysql/mysql.conf.d/mysqld.cnf
# "php" stage
FROM php:8.2-fpm-bookworm AS mycollection

#RUN echo 'deb http://deb.debian.org/debian sid main' >> /etc/apt/sources.list

#install general packets requirements
RUN apt-get update && apt-get install -y libssh-dev unzip nano git

#install php requirements and build php
RUN apt-get install -y \
        libcurl4-openssl-dev\
        libxml2-dev \
        libxslt1-dev \
        libzip-dev \
        libicu-dev \
        libc-client-dev \
        libkrb5-dev \
        libonig-dev \
        librabbitmq-dev \
        libpng-dev \
        libmemcached-dev
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl
RUN docker-php-ext-install -j$(nproc) exif intl pdo_mysql fileinfo iconv soap zip imap mbstring opcache bcmath sockets gd

RUN pecl install amqp xdebug apcu memcached\
    && docker-php-ext-enable amqp xdebug apcu memcached

#update php.ini
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
    && sed -i 's/memory_limit = 128M/memory_limit = 2G/g' "$PHP_INI_DIR/php.ini"

RUN echo 'xdebug.mode=debug' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo 'xdebug.client_host=host.docker.internal' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo 'xdebug.start_with_request=trigger' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo 'xdebug.output_dir=/var/www/html/var/xdebug' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/html

# Install latest composer
COPY docker/php/composer_install.sh .
RUN sh composer_install.sh \
    && composer --version \
    && rm composer_install.sh

# Copy composer files
COPY composer.json .
COPY composer.lock .

# Install composer packages
RUN composer install --no-scripts

# Copy application
COPY . .

# Create and update cache dir to avoid any conlict (DEV ONLY)
RUN mkdir -p ./var/cache && chmod 777 -R ./var/cache

# Clear cache and warmup
RUN chmod 777 bin/console
