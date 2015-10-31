php -S 127.0.0.1:8000 -t PHPCurlClass/ &> /dev/null &
pid="${!}"
phpunit --configuration phpunit.xml
kill "${pid}"
