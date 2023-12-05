FROM ghcr.io/roadrunner-server/roadrunner:2023.2.2 AS roadrunner
FROM php:8.2-cli as base

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

RUN buildDeps="curl libpq-dev libzip-dev libonig-dev openssl libssl-dev libcurl4-openssl-dev  libicu-dev libpng-dev \
     libjpeg62-turbo-dev libfreetype6-dev libmagickwand-6.q16-dev libldap2-dev wget zip unzip libaio1" && \
    apt-get update && \
    apt-get install -y $buildDeps --no-install-recommends && \
    pecl install -o -f redis && \
    rm -rf /tmp/pear && \
    docker-php-ext-enable redis && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install \
        curl \
        dom \
        mbstring \
        simplexml \
        zip \
        opcache \
        gd \
        pdo \
        pdo_mysql \
        sockets \
        bcmath \
        intl

# DEPENDENCIES
RUN apt-get update --fix-missing \
    && apt-get install -y  git \
    && rm -rf /var/lib/apt/lists/*

# KAFKA
RUN git clone --depth 1 https://github.com/edenhill/librdkafka.git \
    && ( \
        cd librdkafka \
        && ./configure \
        && make \
        && make install \
    ) && \
    pecl install rdkafka && \
    docker-php-ext-enable rdkafka

RUN cd ~ && curl -sS https://getcomposer.org/installer -o composer-setup.php && \
	php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
	&& chmod +x /usr/local/bin/composer

RUN mkdir -p /code
WORKDIR /code


