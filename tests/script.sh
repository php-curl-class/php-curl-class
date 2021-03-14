SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

set -x

# Use composer's phpunit and phpcs by adding composer bin directory to the path environment variable.
export PATH="${PWD}/vendor/bin:${PATH}"

errors=0

source "check_syntax.sh"

# Let test server know we should allow testing.
export PHP_CURL_CLASS_TEST_MODE_ENABLED="yes"

# Determine which phpunit to use.
if [[ -f "vendor/bin/phpunit" ]]; then
    phpunit_to_use="vendor/bin/phpunit"
else
    phpunit_to_use="phpunit"
fi

# Run tests.
"${phpunit_to_use}" --version
"${phpunit_to_use}" --configuration "phpunit.xml" --debug --verbose
if [[ "${?}" -ne 0 ]]; then
    echo "Error: phpunit command failed"
    ((errors++))
fi

source "check_coding_standards.sh"

exit "${errors}"
