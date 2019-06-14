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

    service nginx status
}

install_php_fpm() {
    # Install php5-fpm on Travis CI instances. Avoid installing on Docker containers because they are built using fpm
    # images (e.g. "FROM php:5.4-fpm"). Do a basic check to verify that the bypass is only run on Travis CI instances.
    #if [[ ! -z "${TRAVIS}" ]]; then
        $superuser apt-get install -y php5-fpm --allow-unauthenticated
    #fi
}

use_php_fpm() {
    root="$(pwd)/tests/PHPCurlClass/"
    $superuser cat /etc/nginx/sites-enabled/default

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
        include snippets/fastcgi-php.conf;
        fastcgi_param "PHP_CURL_CLASS_TEST_MODE_ENABLED" "yes";
        fastcgi_pass unix:/var/run/php5-fpm.sock;
    }
}
EOF
    $superuser php5-fpm --test
    $superuser service php5-fpm status
    $superuser service php5-fpm stop
    $superuser service php5-fpm start
    $superuser service php5-fpm status

    $superuser cat /etc/nginx/snippets/fastcgi-php.conf
}

reload_nginx() {
    $superuser service nginx status
    $superuser service nginx stop
    $superuser service nginx status
    $superuser service nginx start
    $superuser service nginx status
    sleep 5
    $superuser service nginx status
    sleep 4
    $superuser service nginx start
    sleep 3
    $superuser service nginx status
    sleep 2
    $superuser service nginx start
    sleep 1
    $superuser service nginx status
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

if [[ "${TRAVIS_PHP_VERSION}" == "5.4" ]]; then
    fix_apt_sources
    apt_get_update
    install_nginx
    install_php_fpm

    whoami
    users
    groups

    groups www-data
    usermod --append --groups=sudo www-data
    groups www-data

    fastcgi_pass="unix:/var/run/php5-fpm.sock"
    use_php_fpm
    reload_nginx
    phpunit_shim
fi

curl -v -i 127.0.0.1:8000
