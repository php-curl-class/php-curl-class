install_nginx() {
    $superuser apt-get install -y nginx
}

use_php_fpm() {
    root="$(pwd)/tests/PHPCurlClass"
    $superuser tee /etc/nginx/sites-enabled/default <<EOF
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
        fastcgi_param "PHP_CURL_CLASS_TEST_MODE_ENABLED" "yes";
    }
}
EOF
    $superuser php-fpm --daemonize
}

reload_nginx() {
    $superuser /etc/init.d/nginx restart
}

start_php_servers() {
    for i in $(seq 0 6); do
        port=8000
        (( port += $i ))
        php -S "127.0.0.1:${port}" -t tests/PHPCurlClass/ &
    done
}

set -x
echo "CI_PHP_VERSION: ${CI_PHP_VERSION}"
php -r "var_dump(phpversion());"
php -r "var_dump(curl_version());"

composer self-update
composer install --prefer-source --no-interaction

# Use docker-specific settings.
if [ -f "/.dockerenv" ]; then
    # Skip using sudo.
    superuser=""
    # Use unix socket.
    fastcgi_pass="unix:/var/run/php5-fpm.sock"
else
    # Use sudo.
    superuser="sudo"
    # Use ip socket.
    fastcgi_pass="127.0.0.1:9000"
fi

# Let test server know we should allow testing.
export PHP_CURL_CLASS_TEST_MODE_ENABLED="yes"

if [[ "${CI_PHP_VERSION}" == "5.3" ]]; then
    if ! [ -x "$(command -v add-apt-repository)" ]; then
        $superuser apt-get install -y python-software-properties
        $superuser apt-get install -y software-properties-common
    fi
    $superuser add-apt-repository -y ppa:nginx/development
    $superuser apt-get update
    install_nginx
    $superuser apt-get install -y php5-fpm
    root="$(pwd)/tests/PHPCurlClass"
    $superuser tee /etc/nginx/sites-enabled/default <<EOF
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
        fastcgi_pass ${fastcgi_pass};
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param "PHP_CURL_CLASS_TEST_MODE_ENABLED" "yes";
    }
}
EOF
    $superuser /etc/init.d/php5-fpm start
    reload_nginx
    phpunit_shim
    phpunit_v4_8_shim
    php_v5_3_shim
elif [[ "${CI_PHP_VERSION}" == "5.4" ]]; then
    install_nginx
    use_php_fpm
    reload_nginx
    phpunit_shim
elif [[ "${CI_PHP_VERSION}" == "5.5" ]]; then
    install_nginx
    use_php_fpm
    reload_nginx
    phpunit_shim
elif [[ "${CI_PHP_VERSION}" == "5.6" ]]; then
    install_nginx
    use_php_fpm
    reload_nginx
    phpunit_shim
elif [[ "${CI_PHP_VERSION}" == "7.0" ]]; then
    start_php_servers
elif [[ "${CI_PHP_VERSION}" == "7.1" ]]; then
    start_php_servers
elif [[ "${CI_PHP_VERSION}" == "7.2" ]]; then
    start_php_servers
elif [[ "${CI_PHP_VERSION}" == "7.3" ]]; then
    start_php_servers
elif [[ "${CI_PHP_VERSION}" == "7.4" ]]; then
    start_php_servers
elif [[ "${CI_PHP_VERSION}" == "8.0" ]]; then
    start_php_servers
elif [[ "${CI_PHP_VERSION}" == "nightly" ]]; then
    start_php_servers
fi
