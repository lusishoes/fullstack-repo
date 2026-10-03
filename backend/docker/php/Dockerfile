FROM phpswoole/swoole:php8.4 AS base

ARG USER_ID=1000
ARG GROUP_ID=1000

ENV TZ=UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        git \
        libicu-dev \
        libpq-dev \
        libzip-dev \
        supervisor \
        unzip \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
        bcmath \
        intl \
        pcntl \
        pdo_pgsql \
        zip

RUN git config --global --add safe.directory /var/www

RUN groupadd -g $GROUP_ID appgroup \
    && useradd -u $USER_ID -g appgroup -m appuser

WORKDIR /var/www

COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN mkdir -p /var/log/supervisor

COPY . /var/www
RUN chown -R appuser:appgroup /var/www

#==== DEVELOPMENT ===>

FROM base AS dev

# Node нужен только для `octane:start --watch`: слежение за файлами идёт
# через chokidar. Ставится из репозитория Debian: deb.nodesource.com
# доступен не из всех сетей.
RUN apt-get update \
    && apt-get install -y --no-install-recommends nodejs npm \
    && npm config set fund false --global \
    && npm config set audit false --global \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# pecl.php.net доступен не из всех сетей: Xdebug собирается из исходников
# с GitHub, версия закреплена.
ARG XDEBUG_VERSION=3.5.0
RUN curl -fsSL "https://github.com/xdebug/xdebug/archive/refs/tags/${XDEBUG_VERSION}.tar.gz" \
        | tar -xz -C /tmp \
    && cd /tmp/xdebug-${XDEBUG_VERSION} \
    && phpize \
    && ./configure --enable-xdebug \
    && make -j"$(nproc)" \
    && make install \
    && docker-php-ext-enable xdebug \
    && rm -rf /tmp/xdebug-${XDEBUG_VERSION}
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY docker/php/boot/10-permissions.sh /usr/local/boot/10-permissions.sh
COPY docker/php/supervisord.dev.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8000
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

#==== PRODUCTION ===>

FROM base AS vendor

RUN composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --no-progress \
        --optimize-autoloader \
    && php artisan package:discover --ansi

FROM base AS prod

COPY --from=vendor --chown=appuser:appgroup /var/www/vendor /var/www/vendor

ARG APP_VERSION=dev
ENV APP_VERSION=$APP_VERSION

EXPOSE 8000
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
