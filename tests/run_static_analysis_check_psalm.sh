#!/usr/bin/env bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

source "set_vars.inc.sh"

# Run commands from the project root directory.
pushd ..

set -x

# TODO: Remove exclusion that skips running psalm on PHP 8.4 when psalm
# supports PHP 8.4 (https://github.com/vimeo/psalm/issues/11107).
if [[ $(echo "${CI_PHP_VERSION} < 8.4" | bc -l) -eq 1 ]]; then
    vendor/bin/psalm --config="tests/psalm.xml"
    if [[ "${?}" -ne 0 ]]; then
        echo "Error: psalm static analysis check failed"
        errors+=("psalm static analysis check failed")
    fi
else
    echo "Skipped running psalm check"
fi

popd
