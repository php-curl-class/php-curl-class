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

phpunit_v8_1_shim() {
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

# Determine which phpunit to use.
if [[ -f "../vendor/bin/phpunit" ]]; then
    phpunit_to_use="../vendor/bin/phpunit"
else
    phpunit_to_use="phpunit"
fi

phpunit_version="$("${phpunit_to_use}" --version | grep -Eo "[0-9]+\.[0-9]+\.[0-9]+")"
echo "${phpunit_version}"

if [[ "${phpunit_version}" == "6.5."* ]]; then
    phpunit_v6_5_shim
elif [[ "${phpunit_version}" == "7.5."* ]]; then
    phpunit_v7_5_shim
elif [[ "${phpunit_version}" == "8.1."* ]]; then
    phpunit_v8_1_shim
fi

if [[ "${CI_PHP_VERSION}" == "7.0" ]]; then
    start_php_servers
elif [[ "${CI_PHP_VERSION}" == "7.1" ]]; then
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
