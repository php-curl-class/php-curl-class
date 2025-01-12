#!/usr/bin/env bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

source "set_vars.inc.sh"

# Run commands from the project root directory.
pushd ..

set -x

if [[ $(echo "${CI_PHP_VERSION} >= 7.4" | bc -l) -eq 1 ]]; then
    vendor/bin/phpstan analyse --ansi --configuration="phpstan.neon" .
    if [[ "${?}" -ne 0 ]]; then
        echo "Error: phpstan static analysis check failed"
        errors+=("phpstan static analysis check failed")
    fi
else
    echo "Skipped running phpstan check"
fi

popd
