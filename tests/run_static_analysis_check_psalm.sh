#!/usr/bin/env bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

source "set_vars.inc.sh"

# Run commands from the project root directory.
pushd ..

set -x

if [[ ! -f "vendor/bin/psalm" ]]; then
    echo "⚠️ Skipped running psalm static analysis check"
    warnings+=("Skipped running psalm static analysis check")
else
    vendor/bin/psalm --version
    vendor/bin/psalm --config="tests/psalm.xml"

    if [[ "${?}" -ne 0 ]]; then
        echo "❌ Error: psalm static analysis check failed"
        errors+=("psalm static analysis check failed")
    fi
fi

popd
