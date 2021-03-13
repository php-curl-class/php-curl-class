# Run image to create container and attach to it.

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
set -x
cd "${SCRIPT_DIR}/../../.."
project_dir="${PWD}"

docker run \
    --interactive \
    --mount "type=bind,src=${project_dir},dst=/data,readonly=true" \
    --name="php80" \
    --rm \
    --tty \
    "php-curl-class/php80" /bin/bash
