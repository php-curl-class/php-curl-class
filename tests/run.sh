SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

set -x

# Let test server know we should allow testing.
export PHP_CURL_CLASS_TEST_MODE_ENABLED="yes"

# Start test server.
php -S 127.0.0.1:8000 -t PHPCurlClass/ &> /dev/null &
pid="${!}"

# Determine which phpunit to use.
if [[ -f "../vendor/phpunit/phpunit/phpunit" ]]; then
    phpunit_to_use="../vendor/phpunit/phpunit/phpunit"
else
    phpunit_to_use="phpunit"
fi

# Run tests.
extra_args="${@}"
"${phpunit_to_use}" \
    --configuration "phpunit.xml" \
    --debug \
    --verbose \
    ${extra_args}
kill "${pid}"
