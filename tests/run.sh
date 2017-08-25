set -x

SCRIPT_PATH="$(cd "$(dirname "$0")"; pwd -P)"
cd "${SCRIPT_PATH}"

php -S 127.0.0.1:8000 -t PHPCurlClass/ &> /dev/null &
pid="${!}"
extra_args="${@}"
phpunit \
    --configuration "phpunit.xml" \
    --debug \
    --verbose \
    ${extra_args}
kill "${pid}"
