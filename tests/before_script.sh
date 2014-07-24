if [[ "${TRAVIS_PHP_VERSION}" == "5.4" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "5.5" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "5.6" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "hhvm" ]]; then
    sudo add-apt-repository -y ppa:nginx/stable
    sudo apt-get update
    sudo apt-get install -y nginx
    sudo /usr/share/hhvm/install_fastcgi.sh
    root="$(pwd)/tests/PHPCurlClass"
    root="${root//\//\\/}"
    sudo sed --in-place --regexp-extended 's/listen 80 default_server;/listen 8000 default_server;/' /etc/nginx/sites-enabled/default
    sudo sed --in-place --regexp-extended "s/root \/usr\/share\/nginx\/html;/root ${root};/" /etc/nginx/sites-enabled/default
    sudo sed --in-place --regexp-extended 's/index index.html index.htm;/index index.php;/' /etc/nginx/sites-enabled/default
    sudo /etc/init.d/nginx restart
fi
