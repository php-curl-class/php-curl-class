#!/usr/bin/env bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

source "set_vars.inc.sh"

# Run commands from the project root directory.
pushd ..

set -x

vendor/bin/psalm --version

if [[ $(echo "${CI_PHP_VERSION} == 7.4" | bc -l) -eq 1 ]]; then
    vendor/bin/psalm --config="tests/psalm_7.4.xml"
else
    vendor/bin/psalm --config="tests/psalm.xml"
fi

if [[ "${?}" -ne 0 ]]; then
    echo "❌ Error: psalm static analysis check failed"
    errors+=("psalm static analysis check failed")
fi

popd
