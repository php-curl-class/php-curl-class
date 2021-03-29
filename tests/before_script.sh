set -x

echo "CI_PHP_VERSION: ${CI_PHP_VERSION}"
php -r "var_dump(phpversion());"
php -r "var_dump(curl_version());"

composer self-update
composer install --prefer-source --no-interaction

# Let test server know we should allow testing.
export PHP_CURL_CLASS_TEST_MODE_ENABLED="yes"

# Start test servers. Run servers on different ports to allow simultaneous
# requests without blocking.
server_count=7
for i in $(seq 0 $(("${server_count}" - 1))); do
    port=8000
    (( port += $i ))

    php -S "127.0.0.1:${port}" -t tests/PHPCurlClass/ &
done
