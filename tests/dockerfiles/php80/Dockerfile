FROM php:8.0-cli
ENV DEBIAN_FRONTEND noninteractive

RUN apt-get --assume-yes --quiet update

RUN apt-get --assume-yes --quiet install git && \
    apt-get --assume-yes --quiet install libpng-dev && \
    apt-get --assume-yes --quiet install rsync && \
    apt-get --assume-yes --quiet install zip

RUN curl --silent --show-error "https://getcomposer.org/installer" | php && \
    mv "composer.phar" "/usr/local/bin/composer" && \
    composer global require --no-interaction "phpunit/phpunit"

RUN docker-php-ext-configure gd && \
    docker-php-ext-install gd

ENV PATH /root/.composer/vendor/bin:$PATH
CMD ["bash"]
