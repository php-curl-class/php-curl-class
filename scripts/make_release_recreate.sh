SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

rm -v "make_release_requirements.txt"

pip-compile \
    --output-file="make_release_requirements.txt" \
    "make_release_requirements.in"
