fix_apt_sources() {
    printf "%s\n" \
        "deb http://archive.debian.org/debian/ jessie main"     \
        "deb-src http://archive.debian.org/debian/ jessie main" \
        "deb http://security.debian.org jessie/updates main"    \
        "deb-src http://security.debian.org jessie/updates main"  \
        > /etc/apt/sources.list
}

apt_get_update() {
    $superuser apt-get update
}

install_nginx() {
    # Do a basic check to verify that the bypass is only run on Travis CI instances.
    if [[ -z "${TRAVIS}" ]]; then
        $superuser apt-get install -y nginx
    else
        # Use --allow-unauthenticated flag to fix these messages:
        #   - "WARNING: The following packages cannot be authenticated! [...]"
        #   - "E: There were unauthenticated packages and -y was used without --allow-unauthenticated"
        $superuser apt-get install -y nginx --allow-unauthenticated
    fi
}

install_php_fpm() {
    # Install php5-fpm on Travis CI instances. Avoid installing on Docker containers because they are built using fpm
    # images (e.g. "FROM php:5.4-fpm"). Do a basic check to verify that the bypass is only run on Travis CI instances.
    if [[ ! -z "${TRAVIS}" ]]; then
        $superuser apt-get install -y php5-fpm --allow-unauthenticated
    fi
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
        fastcgi_pass ${fastcgi_pass};
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param "PHP_CURL_CLASS_TEST_MODE_ENABLED" "yes";
    }
}
EOF
    $superuser service php5-fpm reload
}

reload_nginx() {
    $superuser /etc/init.d/nginx restart
}

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

# Use docker-specific settings.
if [ -f "/.dockerenv" ]; then
    # Skip using sudo.
    superuser=""
else
    # Use sudo.
    superuser="sudo"
fi

# Let test server know we should allow testing.
export PHP_CURL_CLASS_TEST_MODE_ENABLED="yes"

if [[ "${TRAVIS_PHP_VERSION}" == "5.3" ]]; then
    if ! [ -x "$(command -v add-apt-repository)" ]; then
        $superuser apt-get install -y python-software-properties
        $superuser apt-get install -y software-properties-common
    fi
    $superuser add-apt-repository -y ppa:nginx/development
    apt_get_update
    install_nginx
    install_php_fpm
    fastcgi_pass="127.0.0.1:9000"
    use_php_fpm
    reload_nginx
    phpunit_shim
elif [[ "${TRAVIS_PHP_VERSION}" == "5.4" ]]; then
    fix_apt_sources
    apt_get_update
    install_nginx
    install_php_fpm
    fastcgi_pass="unix:/var/run/php5-fpm.sock"
    use_php_fpm
    reload_nginx
    phpunit_shim
elif [[ "${TRAVIS_PHP_VERSION}" == "5.5" ]]; then
    fix_apt_sources
    apt_get_update
    install_nginx
    install_php_fpm
    fastcgi_pass="unix:/var/run/php5-fpm.sock"
    use_php_fpm
    reload_nginx
    phpunit_shim
elif [[ "${TRAVIS_PHP_VERSION}" == "5.6" ]]; then
    fix_apt_sources
    apt_get_update
    install_nginx
    install_php_fpm
    fastcgi_pass="unix:/var/run/php5-fpm.sock"
    use_php_fpm
    reload_nginx
    phpunit_shim
elif [[ "${TRAVIS_PHP_VERSION}" == "7.0" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "7.1" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "7.2" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "7.3" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
elif [[ "${TRAVIS_PHP_VERSION}" == "hhvm" || "${TRAVIS_PHP_VERSION}" == "hhvm-nightly" ]]; then
    curl "https://nginx.org/keys/nginx_signing.key" | sudo apt-key add -
    echo "deb https://nginx.org/packages/mainline/ubuntu/ trusty nginx" | sudo tee -a /etc/apt/sources.list
    echo "deb-src https://nginx.org/packages/mainline/ubuntu/ trusty nginx" | sudo tee -a /etc/apt/sources.list
    sudo apt-get update
    sudo apt-get install -y nginx
    sudo tee /etc/nginx/conf.d/default.conf <<"EOF"
server {
    listen 8000 default_server;
    server_name localhost;
    root /usr/share/nginx/html;
    location / {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
}
EOF
    sudo /etc/init.d/hhvm restart
    sleep 5
    sudo service nginx stop
    sleep 5
    sudo service nginx start

    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    root="${SCRIPT_DIR}/PHPCurlClass"
    sudo cp -v "${root}/"* "/usr/share/nginx/html/"

    # Use an older version of PHPUnit for HHVM builds so that unit tests can be
    # started. HHVM 3.18 (PHP_VERSION=PHP 5.6.99-hhvm) is the last version to
    # run on Trusty yet PHPUnit 6 requires PHP 7.0 or PHP 7.1.
    # Avoids error:
    #   This version of PHPUnit is supported on PHP 7.0 and PHP 7.1.
    #   You are using PHP 5.6.99-hhvm (/usr/bin/hhvm).
    if [[ "${TRAVIS_PHP_VERSION}" == "hhvm" ]]; then
        phpunit_shim
        composer require phpunit/phpunit:5.7.*
    fi
elif [[ "${TRAVIS_PHP_VERSION}" == "nightly" ]]; then
    php -S 127.0.0.1:8000 -t tests/PHPCurlClass/ &
fi
