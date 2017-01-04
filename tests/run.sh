set -x
php -S 127.0.0.1:8000 -t PHPCurlClass/ &> /dev/null &
pid="${!}"
extra_args="${@}"
phpunit \
    --configuration phpunit.xml \
    --debug \
    --verbose \
    ${extra_args}
kill "${pid}"
