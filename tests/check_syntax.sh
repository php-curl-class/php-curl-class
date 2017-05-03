DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

cd "${DIR}"
phpcs \
    --extensions="php" \
    --ignore="*/vendor/*" \
    --standard="ruleset.xml" \
    -p \
    -s \
    ..
