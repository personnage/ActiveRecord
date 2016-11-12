FROM php:7-fpm
MAINTAINER Kirill Lyubaev <lubaev.ka@gmail.com>

RUN apt-get update -y \
    && apt-get install -y --no-install-recommends \
        git \
        zlib1g-dev \
        libghc-postgresql-libpq-dev \
    && rm -r /var/lib/apt/lists/*

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/include/postgresql/ \
    && docker-php-ext-install -j$(nproc) zip pgsql pdo_pgsql

RUN { \
        echo "short_open_tag=Off"; \
        echo "date.timezone=${PHP_TIMEZONE:-UTC}"; \
    } > $PHP_INI_DIR/conf.d/custom-settings.ini


ENV COMPOSER_HASH "aa96f26c2b67226a324c27919f1eb05f21c248b987e6195cad9690d5c1ff713d53020a02ac8c217dbf90a7eacc9d141d"

RUN php -r "copy('https://getcomposer.org/installer', sys_get_temp_dir() . '/composer-setup.php');" \
    && php -r "if (hash_file('SHA384', sys_get_temp_dir() . '/composer-setup.php') !== getenv('COMPOSER_HASH')) { echo 'Installer corrupt'; unlink('composer-setup.php'); exit(1); } echo PHP_EOL;" \
    && php /tmp/composer-setup.php \
        --filename=composer \
        --install-dir=/usr/local/bin \
        --no-ansi \
        --snapshot \
    && php -r "unlink(sys_get_temp_dir() . '/composer-setup.php');"

# https://getcomposer.org/doc/03-cli.md#environment-variables
ENV COMPOSER_HOME /composer
ENV COMPOSER_PROCESS_TIMEOUT 45
ENV COMPOSER_NO_INTERACTION 1
ENV COMPOSER_DISABLE_XDEBUG_WARN 1
ENV COMPOSER_ALLOW_SUPERUSER 1

ENV PATH "/composer/vendor/bin:$PATH"

ADD ./ ./

# https://github.com/boot2docker/boot2docker/issues/587#issuecomment-114868208
RUN usermod -u ${DOCKER_USER_ID:-1000} www-data

VOLUME ["/var/www/html"]
