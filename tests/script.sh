SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

# Use composer's phpunit and phpcs by adding composer bin directory to the path environment variable.
export PATH="${PWD}/vendor/bin:${PATH}"

errors=()

source "check_syntax.sh"

# Let test server know we should allow testing.
export PHP_CURL_CLASS_TEST_MODE_ENABLED="yes"

# Determine which phpunit to use.
if [[ -f "../vendor/bin/phpunit" ]]; then
    phpunit_to_use="../vendor/bin/phpunit"
else
    phpunit_to_use="phpunit"
fi

# Run tests.
"${phpunit_to_use}" --version
"${phpunit_to_use}" --configuration "phpunit.xml" --debug --verbose
if [[ "${?}" -ne 0 ]]; then
    echo "Error: phpunit command failed"
    errors+=("phpunit command failed")
fi

source "check_coding_standards.sh"

error_count="${#errors[@]}"
if [[ "${error_count}" -ge 1 ]]; then
    echo -e "\nErrors found: ${error_count}"

    iter=0
    for value in "${errors[@]}"; do
        ((iter++))
        echo -e "\nError ${iter} of ${error_count}:"
        echo "${value}" | perl -pe 's/^(.*)$/\t\1/'
    done
fi

exit "${#errors[@]}"
