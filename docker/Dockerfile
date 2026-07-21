FROM dunglas/frankenphp

RUN install-php-extensions intl opcache pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
