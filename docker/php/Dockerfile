FROM composer:2.3 AS composer

FROM php:8.1-bullseye

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt update && apt install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd

WORKDIR /var/www/app

CMD [ "php", "-S", "0.0.0.0:80", "-t", "public/" ]
