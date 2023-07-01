#!/usr/bin/env bash

list_of_files="${@}"
for file in $list_of_files; do
    if [[ "${file}" == "composer.json" ]]; then
        $(which composer) validate
        exit "${?}"
    else
        echo "unsupported file: ${file}"
        exit 1
    fi
done

exit 0
