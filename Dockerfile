ARG phpV=8.1.9
FROM php:${phpV}-alpine AS builder

# install composer
COPY build/composer-setup.php .
#ADD https://getcomposer.org/installer composer-setup.php
RUN ["php", "composer-setup.php", "--install-dir=/bin", "--filename=composer"]

# Install unzip for symfony/flex
RUN ["apk", "add", "--no-cache", "unzip"]

WORKDIR /app
COPY bin /app/bin/

COPY composer.json /app/

# install php requirements
RUN ["composer", "install"]
RUN ["composer", "require", "predis/predis"]


FROM php:${phpV}-alpine AS symfony
# add symfony-cli package
RUN ["apk", "add", "--no-cache", "bash"]
#$ curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | sudo -E bash \
COPY build/symfony-cli-alpine.sh /tmp/
RUN ["bash", "/tmp/symfony-cli-alpine.sh"]
# Install (unzip for symfony/flex), symfony,
RUN ["apk", "add", "--no-cache", "symfony-cli"]
RUN ["rm", "/tmp/symfony-cli-alpine.sh"]
#RUN set -eux; \
#        apk update; \
#        apk add php-soap; \

RUN apk add libxml2-dev
## install mysql driver
RUN docker-php-ext-install pdo_mysql soap
#RUN docker-php-ext-install opcache
RUN docker-php-ext-enable pdo_mysql soap






FROM symfony AS runner
WORKDIR /app
COPY --from=builder /app /app
COPY config /app/config/
COPY migrations /app/migrations/
COPY public /app/public/
COPY src /app/src/
COPY templates /app/templates/
COPY translations /app/translations/
COPY .env /app/
COPY db_check.php /app/
COPY phpunit.xml.dist /app/


# migrate database
#RUN ["symfony", "console", "doctrine:migrations:migrate", "--no-interaction"]
RUN (crontab -l ; echo "* * * * * cd /app && symfony console app:host:update-business-class") | crontab -

EXPOSE 8000
RUN ["symfony", "console", "lexik:jwt:generate-keypair", "--overwrite", "--no-interaction"]
ENTRYPOINT ["symfony", "server:ca:install"]
COPY build/entrypoint.sh /app
ENTRYPOINT ["bash", "/app/entrypoint.sh"]
