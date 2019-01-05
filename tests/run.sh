SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

set -x

# Let test server know we should allow testing.
export PHP_CURL_CLASS_TEST_MODE_ENABLED="yes"

php -S 127.0.0.1:8000 -t PHPCurlClass/ &> /dev/null &
pid="${!}"
extra_args="${@}"
phpunit \
    --configuration "phpunit.xml" \
    --debug \
    --verbose \
    ${extra_args}
kill "${pid}"
