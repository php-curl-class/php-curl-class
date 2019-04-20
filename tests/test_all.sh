SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

find . -type d -mindepth 2 -path "*/dockerfiles/*" | sort --reverse | while read directory; do
    echo "Running ${directory}" &&
      pushd "${directory}" &&
      bash "1_build.sh" &&
      bash "2_start.sh" &&
      bash "3_test.sh" &&
      bash "4_stop.sh" &&
      popd
done
