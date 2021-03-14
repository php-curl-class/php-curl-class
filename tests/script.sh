SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

set -x

# Use composer's phpunit and phpcs by adding composer bin directory to the path environment variable.
export PATH="${PWD}/vendor/bin:${PATH}"

errors=0

source "check_syntax.sh"

# Let test server know we should allow testing.
export PHP_CURL_CLASS_TEST_MODE_ENABLED="yes"

# Run tests.
phpunit --version
phpunit --configuration "phpunit.xml" --debug --verbose
if [[ "${?}" -ne 0 ]]; then
    echo "Error: phpunit command failed"
    ((errors++))
fi

source "check_coding_standards.sh"

exit "${errors}"
