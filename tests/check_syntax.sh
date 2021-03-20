SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

# Check syntax in php files. Use `xargs' over `find -exec' as xargs exits with a value of 1 when any command errors.
find .. -type "f" -iname "*.php" ! -path "*/vendor/*" | xargs -L "1" php -l
if [[ "${?}" -ne 0 ]]; then
    echo "Error: php syntax checks failed"
    errors+=("php syntax checks failed")
fi
