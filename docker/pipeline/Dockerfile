FROM ubuntu:14.04

MAINTAINER ZIED CHALLOUF

ENV DEBIAN_FRONTEND noninteractive
ENV SYMFONY_ENV test

RUN apt-get update && \
    apt-get install -y software-properties-common && \
    LANG=C.UTF-8 add-apt-repository ppa:ondrej/php && \
    apt-get update && \
    apt-get -y install curl ca-certificates git

RUN apt-get install -y mysql-client mysql-server supervisor unzip

RUN apt-get install -y --no-install-recommends \
    php5.6 \
    php5.6-dom \
    php5.6-mbstring \
    php5.6-curl \
    php5.6-cli \
    php5.6-mysql \
    php5.6-intl \
    php5.6-xml \
    php5.6-xmlreader \
    php5.6-mcrypt \
    php5.6-curl \
    php5.6-json \
    php5.6-dom \
    php5.6-phar \
    php5.6-mbstring \
    php5.6-zip \
    php5.6-pdo \
    php5.6-xdebug


RUN php -v

RUN curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

RUN sed -i -e"s/^bind-address\s*=\s*127.0.0.1/bind-address = 0.0.0.0/" /etc/mysql/my.cnf

ADD set-mysql-pwd.sh /tmp/set-mysql-pwd.sh

RUN /bin/sh /tmp/set-mysql-pwd.sh

ADD supervisord.conf    /etc/supervisor/conf.d/supervisord.conf

WORKDIR /app/

CMD /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf -n
