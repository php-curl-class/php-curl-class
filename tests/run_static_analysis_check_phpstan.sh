#!/usr/bin/env bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

source "set_vars.inc.sh"

# Run commands from the project root directory.
pushd ..

set -x

if true; then

    phpstan_args=(--ansi --configuration="tests/phpstan.neon")

    # Increase memory limit on local development.
    if [[ "${CI}" != "true" ]]; then
        phpstan_args+=(--memory-limit=256M)
    fi

    vendor/bin/phpstan --version
    vendor/bin/phpstan analyse "${phpstan_args[@]}" .
    if [[ "${?}" -ne 0 ]]; then
        echo "❌ Error: phpstan static analysis check failed"
        errors+=("phpstan static analysis check failed")
    fi
else
    echo "⚠️ Skipped running phpstan check"
    warnings+=("Skipped running phpstan check")
fi

popd
