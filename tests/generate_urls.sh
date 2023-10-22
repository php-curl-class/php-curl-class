SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

set -x

python3 "generate_urls.py" &&
    ([[ -f "urls.csv.gz" ]] && rm --verbose "urls.csv.gz" || exit 0) &&
    gzip --verbose --best --no-name "urls.csv" &&
    gzip --verbose --test "urls.csv.gz" &&
    mv --verbose "urls.csv.gz" "PHPCurlClass/urls.csv.gz"
