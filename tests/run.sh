#!/usr/bin/env bash

set -x

# Use composer's phpunit and phpcs by adding composer bin directory to the path environment variable.
export PATH="${PWD}/vendor/bin:${PATH}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

echo "CI_PHP_VERSION: ${CI_PHP_VERSION}"
echo "CI_PHP_FUTURE_RELEASE: ${CI_PHP_FUTURE_RELEASE}"
php -r "var_dump(phpversion());"
php -r "var_dump(curl_version());"

errors=()

source "check_syntax.sh"

source "check_coding_standards.sh"

source "run_phpunit.sh"

source "display_errors.inc.sh"

if [[ "${CI_PHP_FUTURE_RELEASE}" != "true" ]]; then
    exit "${#errors[@]}"
elif [[ "${#errors[@]}" -ne 0 ]]; then
    echo "One or more tests failed, but allowed as the CI_PHP_FUTURE_RELEASE flag is on for PHP version ${CI_PHP_VERSION}."
fi
