FROM php:7.4-cli

WORKDIR /app

RUN apt update
RUN apt install -y iproute2

# XDebug
RUN pecl install xdebug-2.9.6
RUN docker-php-ext-enable xdebug
RUN touch /var/log/xdebug.log && chmod a+rw /var/log/xdebug.log
ENV XDEBUG_CONFIG="remote_enable=1 remote_autostart=1 remote_connect_back=0 remote_host=host.docker.internal remote_port=9000 remote_log=/var/log/xdebug.log"
