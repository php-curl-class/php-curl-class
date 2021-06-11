php_v5_3_shim() {
    remove_double_colon_class_name_resolution
}

phpunit_shim() {
    # -class CurlTest extends \PHPUnit\Framework\TestCase
    # +class CurlTest extends \PHPUnit_Framework_TestCase
    find='class CurlTest extends \\PHPUnit\\Framework\\TestCase'
    replace='class CurlTest extends \\PHPUnit_Framework_TestCase'
    sed -i'' -e"s/${find}/${replace}/" "./PHPCurlClass/PHP"*

    # -\PHPUnit\Framework\Assert
    # +\PHPUnit_Framework_Assert
    find='\\PHPUnit\\Framework\\Assert'
    replace='\\PHPUnit_Framework_Assert'
    sed -i'' -e"s/${find}/${replace}/" "./PHPCurlClass/PHP"*
    sed -i'' -e"s/${find}/${replace}/" "./PHPCurlClass/Helper.php"

    # -\PHPUnit\Framework\Error\Warning
    # +\PHPUnit_Framework_Error_Warning
    find='\\PHPUnit\\Framework\\Error\\Warning'
    replace='\\PHPUnit_Framework_Error_Warning'
    sed -i'' -e"s/${find}/${replace}/" "./PHPCurlClass/PHP"*
}

remove_double_colon_class_name_resolution() {
    sed -i'' -e"/::class/d" "./PHPCurlClass/PHP"*
}

remove_expectWarning() {
    # Fix "Call to undefined method CurlTest\CurlTest::expectWarning()".
    sed -i'' -e"/->expectWarning(/d" "./PHPCurlClass/PHP"*
}

replace_assertStringContainsString() {
    # -->assertStringContainsString(
    # +->assertContains(
    find='->assertStringContainsString('
    replace='->assertContains('
    sed -i'' -e"s/${find}/${replace}/" "./PHPCurlClass/PHP"*
}

phpunit_v4_8_shim() {
    replace_assertStringContainsString
}

phpunit_v6_5_shim() {
    remove_expectWarning
    replace_assertStringContainsString
}

phpunit_v7_5_shim() {
    remove_expectWarning
}

phpunit_v8_1_shim() {
    remove_expectWarning
}

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
    # Start test servers. Run servers on different ports to allow simultaneous
    # requests without blocking.
    server_count=7
    for i in $(seq 0 $(("${server_count}" - 1))); do
        port=8000
        (( port += $i ))

        php -S "127.0.0.1:${port}" -t PHPCurlClass/ &
    done
}

set -x

composer self-update
composer install --prefer-source --no-interaction

# Use composer's phpunit and phpcs by adding composer bin directory to the path environment variable.
export PATH="${PWD}/vendor/bin:${PATH}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

echo "CI_PHP_VERSION: ${CI_PHP_VERSION}"
php -r "var_dump(phpversion());"
php -r "var_dump(curl_version());"

# Let test server know we should allow testing.
export PHP_CURL_CLASS_TEST_MODE_ENABLED="yes"

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
fi

errors=()

source "check_syntax.sh"

# Determine which phpunit to use.
if [[ -f "../vendor/bin/phpunit" ]]; then
    phpunit_to_use="../vendor/bin/phpunit"
else
    phpunit_to_use="phpunit"
fi

phpunit_version="$("${phpunit_to_use}" --version | grep -Eo "[0-9]+\.[0-9]+\.[0-9]+")"
echo "phpunit_version: ${phpunit_version}"

if [[ "${phpunit_version}" == "6.5."* ]]; then
    phpunit_v6_5_shim
elif [[ "${phpunit_version}" == "7.5."* ]]; then
    phpunit_v7_5_shim
elif [[ "${phpunit_version}" == "8.1."* ]]; then
    phpunit_v8_1_shim
fi

# Run tests.
"${phpunit_to_use}" --version
"${phpunit_to_use}" \
    --configuration "phpunit.xml" \
    --debug \
    --verbose
if [[ "${?}" -ne 0 ]]; then
    echo "Error: phpunit command failed"
    errors+=("phpunit command failed")
fi

source "check_coding_standards.sh"

set +x

error_count="${#errors[@]}"
if [[ "${error_count}" -ge 1 ]]; then
    echo -e "\nErrors found: ${error_count}"

    iter=0
    for value in "${errors[@]}"; do
        ((iter++))
        echo -e "\nError ${iter} of ${error_count}:"
        echo "${value}" | perl -pe 's/^(.*)$/\t\1/'
    done
fi

# Stop test servers.
for pid in "${pids[@]}"; do
  kill "${pid}" &> /dev/null &
done

exit "${#errors[@]}"
