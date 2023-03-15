FROM php:7.3.19-fpm

ARG HOST_UID

WORKDIR /usr/local/etc/php-fpm.d

USER root

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# system
RUN apt-get update && apt-get install --no-install-recommends -qy \
        openssl \
        libpq-dev \
        libxslt-dev \
        unzip \
        git \
        libzip-dev \
        locales \
        wget \
        unzip

# Locale
RUN sed -i -e \
  's/# ru_RU.UTF-8 UTF-8/ru_RU.UTF-8 UTF-8/' /etc/locale.gen \
   && locale-gen

ENV LANG ru_RU.UTF-8
ENV LANGUAGE ru_RU:ru
ENV LC_LANG ru_RU.UTF-8
ENV LC_ALL ru_RU.UTF-8

RUN chmod 777 -R /var/www

RUN apt update && apt upgrade -y

# clean
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
WORKDIR /var/www

# xdebug
RUN pecl install xdebug-2.7.2 && docker-php-ext-enable xdebug

# extensions
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install pdo_pgsql pgsql xsl sockets bcmath pcntl zip

# configs
ADD php.ini /usr/local/etc/php/php.ini
COPY www.conf /usr/local/etc/php-fpm.d/www.conf

# composer
RUN mkdir -p /var/www/.composer
COPY keys.dev.pub /var/www/.composer/keys.dev.pub
COPY keys.tags.pub /var/www/.composer/keys.tags.pub
COPY cacert.pem /etc/ssl/certs/cacert.pem

# java
RUN mkdir -p /opt/java && \
    cd /opt/java && \
    wget https://github.com/adoptium/temurin8-binaries/releases/download/jdk8u332-b09/OpenJDK8U-jdk_x64_linux_hotspot_8u332b09.tar.gz && \
    tar xf OpenJDK8U-jdk_x64_linux_hotspot_8u332b09.tar.gz && \
    ls -ltha && \
    cd jdk8u332-b09/bin && \
    ./java -version
ENV PATH="/opt/java/jdk8u332-b09/bin:${PATH}"

WORKDIR /nifi/bin/

RUN usermod -u ${HOST_UID} www-data
USER www-data

EXPOSE 9000