set -x

SCRIPT_DIR="$(cd "$(dirname "$0")"; pwd -P)"
cd "${SCRIPT_DIR}"

php -S 127.0.0.1:8000 -t PHPCurlClass/ &> /dev/null &
pid="${!}"
extra_args="${@}"
phpunit \
    --configuration "phpunit.xml" \
    --debug \
    --verbose \
    ${extra_args}
kill "${pid}"
