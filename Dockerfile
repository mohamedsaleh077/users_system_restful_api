FROM 8ct8pus/apache-php-fpm-alpine:2.5.2

WORKDIR /sites/localhost/html/public

#COPY docker/etc/ /docker/etc/

RUN chown -R apache:apache /sites/localhost || true

# composer commands
WORKDIR /sites/localhost/html/public/app
COPY ./website/* /sites/localhost/html/public/

RUN apk add composer
RUN composer install
RUN composer update
RUN composer dump-autoload

EXPOSE 80 443