FROM php:5.5-fpm
ENV DEBIAN_FRONTEND noninteractive

# Fix error:
#     "E: Unable to fetch some archives, maybe run apt-get update or try with --fix-missing?".
RUN printf "%s\n" \
    "deb http://archive.debian.org/debian/ jessie main"     \
    "deb-src http://archive.debian.org/debian/ jessie main" \
    "deb http://security.debian.org jessie/updates main"    \
    "deb-src http://security.debian.org jessie/updates main"  \
    > /etc/apt/sources.list

RUN apt-get --assume-yes --quiet update

RUN apt-get --assume-yes --quiet install git && \
    apt-get --assume-yes --quiet install libpng-dev && \
    apt-get --assume-yes --quiet install zip

RUN curl --silent --show-error "https://getcomposer.org/installer" | php && \
    mv "composer.phar" "/usr/local/bin/composer" && \
    composer global require --no-interaction "phpunit/phpunit"

RUN docker-php-ext-configure gd && \
    docker-php-ext-install gd

ENV PATH /root/.composer/vendor/bin:$PATH
CMD ["bash"]
