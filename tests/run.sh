php -S 127.0.0.1:8000 -t PHPCurlClass/ &
pid="${!}"
phpunit --configuration phpunit.xml
kill "${pid}"
