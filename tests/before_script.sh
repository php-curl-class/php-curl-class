phpunit_shim() {
    # -class CurlTest extends \PHPUnit\Framework\TestCase
    # +class CurlTest extends \PHPUnit_Framework_TestCase
    find='class CurlTest extends \\PHPUnit\\Framework\\TestCase'
    replace='class CurlTest extends \\PHPUnit_Framework_TestCase'
    sed -i'' -e"s/${find}/${replace}/" "$(pwd)/tests/PHPCurlClass/PHP"*

    # -\PHPUnit\Framework\Assert
    # +\PHPUnit_Framework_Assert
    find='\\PHPUnit\\Framework\\Assert'
    replace='\\PHPUnit_Framework_Assert'
    sed -i'' -e"s/${find}/${replace}/" "$(pwd)/tests/PHPCurlClass/PHP"*
    sed -i'' -e"s/${find}/${replace}/" "$(pwd)/tests/PHPCurlClass/Helper.php"

    # -\PHPUnit\Framework\Error\Warning
    # +\PHPUnit_Framework_Error_Warning
    find='\\PHPUnit\\Framework\\Error\\Warning'
    replace='\\PHPUnit_Framework_Error_Warning'
    sed -i'' -e"s/${find}/${replace}/" "$(pwd)/tests/PHPCurlClass/PHP"*
}

set -x
echo "TRAVIS_PHP_VERSION: ${TRAVIS_PHP_VERSION}"
php -r "var_dump(phpversion());"
php -r "var_dump(curl_version());"

composer self-update
composer install --prefer-source --no-interaction

if [[ "${TRAVIS_PHP_VERSION}" == "5.3" ]]; then
    sudo add-apt-repository -y ppa:nginx/development
    sudo apt-get update
    sudo apt-get install -y nginx
    sudo apt-get install -y php5-fpm
    root="$(pwd)/tests/PHPCurlClass"
    sudo tee /etc/nginx/sites-enabled/default <<EOF
server {
    listen 8000 default_server;
    root ${root};
    index index.php;
    server_name localhost;
    location / {
        rewrite ^ /index.php last;
    }
    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }
}
EOF
    sudo /etc/init.d/nginx restart
    phpunit_shim
elif [[ "${TRAVIS_PHP_VERSION}" == "5.4" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
    phpunit_shim
elif [[ "${TRAVIS_PHP_VERSION}" == "5.5" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
    phpunit_shim
elif [[ "${TRAVIS_PHP_VERSION}" == "5.6" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
    phpunit_shim
elif [[ "${TRAVIS_PHP_VERSION}" == "7.0" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "7.1" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "hhvm" || "${TRAVIS_PHP_VERSION}" == "hhvm-nightly" ]]; then
    sudo add-apt-repository -y ppa:nginx/stable
    sudo apt-get update
    sudo apt-get install -y nginx
    root="$(pwd)/tests/PHPCurlClass"
    sudo tee /etc/nginx/sites-enabled/default <<EOF
server {
    listen 8000 default_server;
    root ${root};
    index index.php;
    server_name localhost;
    location / {
        rewrite ^ /index.php last;
    }
}
EOF
    sudo /usr/share/hhvm/install_fastcgi.sh
    sudo /etc/init.d/hhvm restart
    sleep 5
    sudo service nginx stop
    sleep 5
    sudo service nginx start
    phpunit_shim
elif [[ "${TRAVIS_PHP_VERSION}" == "nightly" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
fi
