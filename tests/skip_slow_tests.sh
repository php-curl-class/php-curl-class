#!/usr/bin/env bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

# Let tests know we should skip slow tests.
export PHP_CURL_CLASS_SKIP_SLOW_TESTS="1"

source "run.sh"
