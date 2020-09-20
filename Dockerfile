FROM php:cli

RUN apt-get update
RUN apt-get install -y \
    wget \
    zip \
    unzip \
    libicu-dev \
    zlib1g-dev \
    ssh

RUN docker-php-ext-install \
    pdo_mysql

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=bin --filename=composer \
    && php -r "unlink('composer-setup.php');"
