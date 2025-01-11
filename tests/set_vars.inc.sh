# Use installed version when variable not set.
if [[ -z "${CI_PHP_VERSION}" ]]; then
    CI_PHP_VERSION="$(php -r "echo preg_replace('/^([0-9]+\.[0-9]+)\.[0-9]+/', '\$1', phpversion());")"
fi
