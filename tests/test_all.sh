#!/usr/bin/env bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

find . -type d -mindepth 2 -path "*/dockerfiles/*" | sort --reverse | while read directory; do
    printf '=%.0s' {1..80}
    echo -e "\nRunning ${directory}"
    pushd "${directory}"

    bash "1_build.sh"
    if [[ $? -ne 0 ]]; then
        echo "Error: Build failed for ${directory}"
        exit 1
    fi

    bash "2_start.sh"
    if [[ $? -ne 0 ]]; then
        echo "Error: Start failed for ${directory}"
        exit 1
    fi

    bash "3_test.sh"
    if [[ $? -ne 0 ]]; then
        echo "Error: Test failed for ${directory}"
        exit 1
    fi

    bash "4_stop.sh"
    if [[ $? -ne 0 ]]; then
        echo "Error: Stop failed for ${directory}"
        exit 1
    fi

    popd
done
