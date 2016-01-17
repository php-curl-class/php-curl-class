echo "TRAVIS_PHP_VERSION: ${TRAVIS_PHP_VERSION}"

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
elif [[ "${TRAVIS_PHP_VERSION}" == "5.4" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "5.5" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "5.6" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "7.0" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "hhvm" ]]; then
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
    sudo service nginx restart
elif [[ "${TRAVIS_PHP_VERSION}" == "nightly" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
fi
