SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}/.."

before=$(head -n $(
    grep --context="0" --line-number --max-count="1" "### Available Methods" "README.md" |
    perl -pe 's/^(\d+):.*/\1/') "README.md")

after=$(tail -n +$(
    grep --context="0" --line-number --max-count="1" "### Security" "README.md" |
    perl -pe 's/^(\d+):.*/\1/') "README.md")

echo "${before}" > "README.md"

curl_max_line_number=$(grep --context="0" --line-number --max-count="1" '^}$' "src/Curl/Curl.php" | \
    perl -pe 's/^(\d+):.*/\1/')
echo '```php' >> "README.md"
head -n "${curl_max_line_number}" "src/Curl/Curl.php" | \
    egrep "^    .* function .*" | \
    egrep "^    public" | \
    sort | \
    perl -pe 's/^    public (.* )?function /Curl::/' \
    >> "README.md"
egrep "^    .* function .*" "src/Curl/MultiCurl.php" | \
    egrep "^    public" | \
    sort | \
    perl -pe 's/^    public (.* )?function /MultiCurl::/' \
    >> "README.md"
echo '```' >> "README.md"
echo >> "README.md"

echo "${after}" >> "README.md"

# Update table of contents.
script=$(cat <<'EOF'
    $data = file_get_contents('README.md');
    preg_match_all('/^### ([\w ]+)/m', $data, $matches);
    $toc = [];
    foreach ($matches['1'] as $match) {
        $href = '#' . str_replace(' ', '-', strtolower($match));
        $toc[] = '- [' . $match . '](' . $href . ')';
    }
    $toc = implode("\n", $toc);
    $toc = '---' . "\n\n" . $toc . "\n\n" . '---' . "\n\n";
    $data = preg_replace('/---\n\n(?:- .*\n)+?\n---\n\n/', $toc, $data);
    file_put_contents('README.md', $data);
EOF
)
php --run "${script}"
