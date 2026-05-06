FROM php:8.2-cli

RUN docker-php-ext-install mysqli pdo_mysql

WORKDIR /app

COPY . /app/

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-80} -t /app"]
