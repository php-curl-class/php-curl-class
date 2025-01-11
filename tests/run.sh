#!/usr/bin/env bash

set -x

if [[ "${CI}" == "true" ]]; then
    composer self-update

    # TODO: Add "vimeo/psalm" back into composer.json under "require-dev" when
    # vimeo/psalm supports PHP 8.4 (https://github.com/vimeo/psalm/issues/11107):
    #     "require-dev": {
    #         "vimeo/psalm": ">=5.26.1"
    #     },
    #
    # TODO: Remove this workaround that only installs vimeo/psalm on PHP < 8.4 when
    # vimeo/psalm supports PHP 8.4 (https://github.com/vimeo/psalm/issues/11107):
    if [[ $(echo "${CI_PHP_VERSION} < 8.4" | bc -l) -eq 1 ]]; then
        composer require --dev "vimeo/psalm:>=5.26.1"
    fi

    composer install --prefer-source --no-interaction
fi


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
