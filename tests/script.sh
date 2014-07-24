find . -type "f" -iname "*.php" | xargs -L "1" php -l

if [[ "${TRAVIS_PHP_VERSION}" != "5.3" ]]; then
    cd tests && phpunit --configuration phpunit.xml
fi
