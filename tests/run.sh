SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

set -x

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
    pid="${!}"

    pids["${i}"]="${pid}"
done

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

# Stop test servers.
for pid in "${pids[@]}"; do
  kill "${pid}"
done
