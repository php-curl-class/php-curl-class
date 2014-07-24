if [[ "${TRAVIS_PHP_VERSION}" == "5.4" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "5.5" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "5.6" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "hhvm" ]]; then
    cd tests && hhvm --mode server --port 8000 --config PHPCurlClass/server.hdf &
fi
