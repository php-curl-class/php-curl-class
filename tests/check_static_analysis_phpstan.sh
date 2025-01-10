#!/usr/bin/env bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

# Run commands from the project root directory.
cd ..

set -x

vendor/bin/phpstan analyse --configuration="phpstan.neon" .
