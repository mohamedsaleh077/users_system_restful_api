FROM 8ct8pus/apache-php-fpm-alpine:2.5.2

RUN apk add composer
WORKDIR /sites/localhost/html/public

#COPY docker/etc/ /docker/etc/

RUN chown -R apache:apache /sites/localhost || true

# composer commands
COPY ./* .
WORKDIR /sites/localhost/html/public/app

COPY ./app/composer.json .
RUN composer install
RUN composer dump-autoload

EXPOSE 80 443