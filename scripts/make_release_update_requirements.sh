SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

update_package() {
    local package_name="${1}"
    local package_version="${2}"

    if [[ ! -z "${package_version}" ]]; then
        echo "Updating ${package_name} to version ${package_version}"
        local upgrade_package="${package_name}==${package_version}"
    else
        echo "Updating ${package_name}"
        local upgrade_package="${package_name}"
    fi

    pip-compile \
        --upgrade-package="${upgrade_package}" \
        --output-file="make_release_requirements.txt" \
        "make_release_requirements.in"
}

package_name="${1}"
package_version="${2}"

if [[ -z "${package_name}" ]]; then
    echo "No package name provided. Usage: $0 <package-name> [<package-version>]"
    exit 1
fi

set -x

update_package "${package_name}" "${package_version}"
