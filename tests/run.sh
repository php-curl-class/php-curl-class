#!/usr/bin/env bash

set -x

errors=()

if [[ "${CI}" == "true" ]]; then
    composer self-update

    # Skip attempting to install psalm on future PHP releases to avoid the
    # following error that would otherwise block the remaining tests from
    # running:
    #   Your requirements could not be resolved to an installable set of packages.
    #
    #     Problem 1
    #       - Root composer.json requires vimeo/psalm >=5.26.1 -> satisfiable by vimeo/psalm[5.26.1, 6.0.0, ..., 6.8.8].
    #       - vimeo/psalm 5.26.1 requires php ^7.4 || ~8.0.0 || ~8.1.0 || ~8.2.0 || ~8.3.0 -> your php version (8.5.0-dev) does not satisfy that requirement.
    #       - vimeo/psalm[6.0.0, ..., 6.5.0] require php ~8.1.17 || ~8.2.4 || ~8.3.0 || ~8.4.0 -> your php version (8.5.0-dev) does not satisfy that requirement.
    #       - vimeo/psalm[6.5.1, ..., 6.8.8] require php ~8.1.31 || ~8.2.27 || ~8.3.16 || ~8.4.3 -> your php version (8.5.0-dev) does not satisfy that requirement.
    #
    #   Error: Your requirements could not be resolved to an installable set of packages.
    if ! "${CI_PHP_FUTURE_RELEASE}"; then
        composer require --dev "vimeo/psalm:>=5.26.1"
    fi

    composer install --prefer-source --no-interaction
    if [[ "${?}" -ne 0 ]]; then
        echo "❌ Error: composer install failed"
        errors+=("composer install failed")
    fi
fi

# Use composer's phpunit and phpcs by adding composer bin directory to the path environment variable.
export PATH="${PWD}/vendor/bin:${PATH}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

source "set_vars.inc.sh"

echo "CI_PHP_VERSION: ${CI_PHP_VERSION}"
echo "CI_PHP_FUTURE_RELEASE: ${CI_PHP_FUTURE_RELEASE}"
php -r "var_dump(phpversion());"
php -r "var_dump(curl_version());"

source "run_syntax_check.sh"

source "run_coding_standards_check.sh"

source "run_phpunit.sh"

source "run_static_analysis_check_phpstan.sh"

source "run_static_analysis_check_psalm.sh"

set +x

source "display_errors.inc.sh"

if [[ "${CI_PHP_FUTURE_RELEASE}" != "true" ]]; then
    exit "${#errors[@]}"
elif [[ "${#errors[@]}" -ne 0 ]]; then
    echo "⚠️ One or more tests failed, but allowed as the CI_PHP_FUTURE_RELEASE flag is on for PHP version ${CI_PHP_VERSION}."
fi
