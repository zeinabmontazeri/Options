ARG phpvertion=8.1.9
FROM php:8.1.9

# Install unzip for symfony/flex
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get update && apt-get install -y unzip symfony-cli
#RUN #docker-php-ext-install intl opcache pdo pdo_mysql && pecl install apcu && docker-php-ext-enable apcu \
##    && docker-php-ext-configure zip && docker-php-ext-install zip
#RUN ["apt", "install", "unzip", "-y"]

#install soap
RUN apt-get update && \
 apt-get install -y libxml2-dev
# install mysql driver
RUN docker-php-ext-install pdo_mysql

WORKDIR /app

COPY bin /app/bin/
COPY config /app/config/
COPY migrations /app/migrations/
COPY public /app/public/
COPY src /app/src/
COPY templates /app/templates/
COPY translations /app/translations/
COPY .env /app/
COPY composer.json /app/
COPY composer.lock /app/
COPY db_check.php /app/
COPY phpunit.xml.dist /app/
COPY build/composer-setup.php /tmp/


#ENV APP_ENV="dev"
#ENV APP_SECRET="966415c4f89f0e364d7accd4d944e517"
#ENV MESSENGER_TRANSPORT_DSN="doctrine://default?auto_setup=0"
#ENV DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=14&charset=utf8"
#ENV JWT_SECRET_KEY="%kernel.project_dir%/config/jwt/private.pem"
#ENV JWT_PUBLIC_KEY="%kernel.project_dir%/config/jwt/public.pem"
#ENV JWT_PASSPHRASE="a1cf6b9aac1455b11b0e08a92deb9892"


# Install Composer
RUN ["php", "/tmp/composer-setup.php", "--install-dir=/bin", "--filename=composer"]
RUN ["composer", "require", "nelmio/api-doc-bundle"]
# install php requirements
RUN ["composer", "install"]

RUN ["composer", "update"]

EXPOSE 8000

ENTRYPOINT ["symfony", "server:start", "--no-tls"]
 #ENTRYPOINT ["sh", "-c", "while true; do sleep 1; done"]