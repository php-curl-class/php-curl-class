SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}/.."

before=$(head -n $(
    grep --context="0" --line-number --max-count="1" "### Available Methods" "README.md" |
    perl -pe 's/^(\d+):.*/\1/') "README.md")

after=$(tail -n +$(
    grep --context="0" --line-number --max-count="1" "### Security" "README.md" |
    perl -pe 's/^(\d+):.*/\1/') "README.md")

echo "${before}" > "README.md"

echo '```php' >> "README.md"
find "src/Curl" -type f -name "*Curl*" |
    sort |
    xargs -L 1 -I {} bash -c 'class_name="$(basename --suffix=".php" "{}")" && egrep "^    .* function .*" "{}" | egrep "^    public" | sort | perl -pe "s/^    public (.* )?function /${class_name}::/"' >> "README.md"
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
