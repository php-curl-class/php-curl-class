SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

phpcs \
    --extensions="php" \
    --ignore="*/vendor/*" \
    --standard="ruleset.xml" \
    -p \
    -s \
    ..
