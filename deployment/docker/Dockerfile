FROM ubuntu:16.04

ENV PHPVER 7.2

#RUN locale-gen en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV DEBIAN_FRONTEND noninteractive
RUN LC_ALL=en_US.UTF-8

RUN apt-get update && apt-get install -y \
    software-properties-common \
    language-pack-en-base \
    apt-utils
RUN apt-add-repository ppa:ondrej/php

RUN apt-get update && apt-get install -y \
    php7.2 \
    php7.2-cli \
    php7.2-fpm \
    php7.2-common \
    php7.2-curl \
    php7.2-dev \
    php7.2-gd \
    php7.2-gmp \
    php7.2-imap \
    php7.2-json \
    php7.2-opcache \
    php7.2-mbstring \
    php7.2-readline \
    php7.2-zip \
    php7.2-xsl \
    php7.2-bcmath \
    dh-php \
    php-pear \
    php-apcu \
    php-igbinary \
    php-msgpack \
    php-xml \
    php-memcache \
    php-memcached

ADD server /opt/kaltura/app
#COPY mediainfo /usr/bin/
COPY /opt/kaltura/app/configurations/batchBase.template.ini  /etc/kaltura.d/
#CMD php /opt/kaltura/app/batch/KGenericBatchMgr.class.php php /opt/kaltura/app/configurations/batch >> /var/log/kaltura_batch_log