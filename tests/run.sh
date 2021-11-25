remove_expectWarning() {
    # Fix "Call to undefined method CurlTest\CurlTest::expectWarning()".
    sed -i'' -e"/->expectWarning(/d" "./PHPCurlClass/PHP"*
}

replace_assertStringContainsString() {
    # -->assertStringContainsString(
    # +->assertContains(
    find='->assertStringContainsString('
    replace='->assertContains('
    sed -i'' -e"s/${find}/${replace}/" "./PHPCurlClass/PHP"*
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

php_v7_0_shim() {
    # -protected function setUp(): void
    # +protected function setUp()
    find='protected function setUp(): void'
    replace='protected function setUp()'
    sed -i'' -e"s/${find}/${replace}/" "./PHPCurlClass/PHP"*
}

set -x

# Use composer's phpunit and phpcs by adding composer bin directory to the path environment variable.
export PATH="${PWD}/vendor/bin:${PATH}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

echo "CI_PHP_VERSION: ${CI_PHP_VERSION}"
echo "CI_PHP_FUTURE_RELEASE: ${CI_PHP_FUTURE_RELEASE}"
php -r "var_dump(phpversion());"
php -r "var_dump(curl_version());"

# Let test server know we should allow testing.
export PHP_CURL_CLASS_TEST_MODE_ENABLED="yes"

# Start test servers. Run servers on different ports to allow simultaneous
# requests without blocking.
server_count=7
declare -A pids
for i in $(seq 0 $(("${server_count}" - 1))); do
    port=8000
    (( port += $i ))

    php -S "127.0.0.1:${port}" -t PHPCurlClass/ &> /dev/null &
    pids["${i}"]="${!}"
done

errors=()

source "check_syntax.sh"

# Determine which phpunit to use.
if [[ -f "../vendor/bin/phpunit" ]]; then
    phpunit_to_use="../vendor/bin/phpunit"
else
    phpunit_to_use="phpunit"
fi

phpunit_version="$("${phpunit_to_use}" --version | grep -Eo "[0-9]+\.[0-9]+\.[0-9]+")"
echo "phpunit_version: ${phpunit_version}"

if [[ "${phpunit_version}" == "6.5."* ]]; then
    phpunit_v6_5_shim
elif [[ "${phpunit_version}" == "7.5."* ]]; then
    phpunit_v7_5_shim
elif [[ "${phpunit_version}" == "8.1."* ]]; then
    phpunit_v8_1_shim
fi

if [[ "${CI_PHP_VERSION}" == "7.0" ]]; then
    php_v7_0_shim
fi

# Run tests.
extra_args="${@}"
"${phpunit_to_use}" --version
"${phpunit_to_use}" \
    --configuration "phpunit.xml" \
    --debug \
    --verbose \
    ${extra_args}
if [[ "${?}" -ne 0 ]]; then
    echo "Error: phpunit command failed"
    errors+=("phpunit command failed")
fi

source "check_coding_standards.sh"

set +x

error_count="${#errors[@]}"
if [[ "${error_count}" -ge 1 ]]; then
    echo -e "\nErrors found: ${error_count}"

    iter=0
    for value in "${errors[@]}"; do
        ((iter++))
        echo -e "\nError ${iter} of ${error_count}:"
        echo "${value}" | perl -pe 's/^(.*)$/\t\1/'
    done
fi

# Stop test servers.
for pid in "${pids[@]}"; do
  kill "${pid}" &> /dev/null &
done

if [[ "${CI_PHP_FUTURE_RELEASE}" != "true" ]]; then
    exit "${#errors[@]}"
elif [[ "${#errors[@]}" -ne 0 ]]; then
    echo "One or more tests failed, but allowed as CI_PHP_FUTURE_RELEASE is on for PHP version ${CI_PHP_VERSION}."
fi
