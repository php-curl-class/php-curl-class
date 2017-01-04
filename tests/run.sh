set -x
echo -e "\033[32m please stop nginx or apache server before run\033[39;49m"
php -S 127.0.0.1:8000 -t PHPCurlClass/ &> /dev/null &
pid="${!}"
extra_args="${@}"
phpunit \
    --configuration phpunit.xml \
    --debug \
    --verbose \
    ${extra_args}
kill "${pid}"
