SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

for d in "dockerfiles/"* ; do
    echo "Running ${d}" &&
      pushd "${d}" &&
      bash "1_build.sh" &&
      bash "2_start.sh" &&
      bash "3_test.sh" &&
      bash "4_stop.sh" &&
      popd
done
