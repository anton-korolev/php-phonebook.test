FROM php:8.1-apache
RUN pecl install xdebug-3.3.2 \
	&& docker-php-ext-enable xdebug
COPY ./config/apache2/ /etc/apache2
COPY ./config/php/ /usr/local/etc/php/conf.d/
