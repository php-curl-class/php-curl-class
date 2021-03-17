remove_expectWarning() {
    # Fix "Call to undefined method CurlTest\CurlTest::expectWarning()".
    sed -i'' -e"/->expectWarning(/d" "$(pwd)/tests/PHPCurlClass/PHP"*
}

replace_assertStringContainsString() {
    # -->assertStringContainsString(
    # +->assertContains(
    find='->assertStringContainsString('
    replace='->assertContains('
    sed -i'' -e"s/${find}/${replace}/" "$(pwd)/tests/PHPCurlClass/PHP"*
}

phpunit_v6_5_shim() {
    remove_expectWarning
    replace_assertStringContainsString
}

phpunit_v7_5_shim() {
    remove_expectWarning
}

start_php_servers() {
    for i in $(seq 0 6); do
        port=8000
        (( port += $i ))
        php -S "127.0.0.1:${port}" -t tests/PHPCurlClass/ &
    done
}

set -x
echo "CI_PHP_VERSION: ${CI_PHP_VERSION}"
php -r "var_dump(phpversion());"
php -r "var_dump(curl_version());"

composer self-update
composer install --prefer-source --no-interaction

# Let test server know we should allow testing.
export PHP_CURL_CLASS_TEST_MODE_ENABLED="yes"

if [[ "${CI_PHP_VERSION}" == "7.0" ]]; then
    phpunit_v6_5_shim
    start_php_servers
elif [[ "${CI_PHP_VERSION}" == "7.1" ]]; then
    phpunit_v7_5_shim
    start_php_servers
elif [[ "${CI_PHP_VERSION}" == "7.2" ]]; then
    start_php_servers
elif [[ "${CI_PHP_VERSION}" == "7.3" ]]; then
    start_php_servers
elif [[ "${CI_PHP_VERSION}" == "7.4" ]]; then
    start_php_servers
elif [[ "${CI_PHP_VERSION}" == "8.0" ]]; then
    start_php_servers
elif [[ "${CI_PHP_VERSION}" == "nightly" ]]; then
    start_php_servers
fi
