# Run image to create container.

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
set -x
cd "${SCRIPT_DIR}/../../.."
project_dir="${PWD}"

docker start "php72" ||
    docker run \
        --detach \
        --interactive \
        --mount "type=bind,src=${project_dir},dst=/data,readonly=true" \
        --name="php72" \
        --tty \
        "php-curl-class/php72"
