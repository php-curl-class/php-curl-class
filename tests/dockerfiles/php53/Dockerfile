FROM debian:jessie
ENV DEBIAN_FRONTEND noninteractive

RUN apt-get --assume-yes --quiet update

RUN apt-get --assume-yes --quiet install \
        curl \
        rsync \
        # "/bin/sh: 1: make: not found"
        make \
        # "make[1]: *** No rule to make target '../include/openssl/bio.h', needed by 'cryptlib.o'.  Stop."
        # "configure: error: xml2-config not found. Please check your libxml2 installation."
        libxml2-dev \
        # "make[1]: gcc: Command not found"
        gcc \
        # tar (child): xz: Cannot exec: No such file or directory
        # tar (child): Error is not recoverable: exiting now
        # tar: Child returned status 2
        # tar: Error is not recoverable: exiting now
        xz-utils \
        # "configure: error: Cannot find libz"
        libssl-dev \
        # "configure: error: Please reinstall the libcurl distribution -
        #     easy.h should be in <curl-dir>/include/curl/"
        libcurl4-openssl-dev \
        # "configure: error: png.h not found."
        libpng-dev \
        # "sh: 1: git: not found" (composer).
        git \
        # "Failed to download phpunit/phpunit from dist: The zip extension and unzip command are both missing,
        # skipping."
        zip

# Compile openssl so that php configure --with-openssl works.
RUN mkdir --parents "/tmp/openssl/" && \
    curl --silent --show-error --output "openssl.tar.gz" \
        "https://www.openssl.org/source/openssl-1.0.2k.tar.gz" && \
    tar --extract --file "openssl.tar.gz" --directory "/tmp/openssl/" --strip-components="1" && \
    cd "/tmp/openssl/" && \
    ./config && \
    make && \
    make install && \
    rm -rf "/tmp/openssl/"

RUN mkdir --parents "/usr/src/php/" && \
    curl --silent --show-error --output "php.tar.xz" \
        "https://www.php.net/distributions/php-5.3.29.tar.xz" && \
    tar --extract --file "php.tar.xz" --directory "/usr/src/php/" --strip-components="1" && \
    rm "php.tar.xz"* && \
    cd "/usr/src/php/" && \
    ./configure \
        --enable-mbstring \
        --with-curl \
        --with-gd \
        --with-openssl="/usr/local/ssl" \
        --with-zlib && \
    make --jobs="$(nproc)" && \
    make install && \
    make clean

RUN curl --silent --show-error "https://getcomposer.org/installer" | php && \
    mv "composer.phar" "/usr/local/bin/composer" && \
    composer global require --no-interaction "phpunit/phpunit"

ENV PATH /root/.composer/vendor/bin:$PATH
CMD ["bash"]
