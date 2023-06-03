#!/usr/bin/env bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}/.."

before=$(head -n $(
    grep --context="0" --line-number --max-count="1" "### Available Methods" "README.md" |
    perl -pe 's/^(\d+):.*/\1/') "README.md")

after=$(tail -n +$(
    grep --context="0" --line-number --max-count="1" "### Security" "README.md" |
    perl -pe 's/^(\d+):.*/\1/') "README.md")

echo "${before}" > "README.md"

basecurl_path="src/Curl/BaseCurl.php"
curl_path="src/Curl/Curl.php"
multicurl_path="src/Curl/MultiCurl.php"

echo '```php' >> "README.md"

curl_class_name="$(basename --suffix=".php" "${curl_path}")" &&
curl_fns="$(
    grep --extended-regexp "^    .* function .*" "${curl_path}" |
    grep --extended-regexp "^    public" |
    perl -pe "s/^    public (.* )?function /${curl_class_name}::/")"

multicurl_class_name="$(basename --suffix=".php" "${multicurl_path}")" &&
multicurl_fns="$(
    grep --extended-regexp "^    .* function .*" "${multicurl_path}" |
    grep --extended-regexp "^    public" |
    perl -pe "s/^    public (.* )?function /${multicurl_class_name}::/")"

common_fns="$(
    grep --extended-regexp "^    .* function .*" "${basecurl_path}" |
    grep --extended-regexp "^    public" |
    perl -pe "s/^    public .* ?function (.*)/Curl::\1\nMultiCurl::\1/")"

echo "${curl_fns}
${multicurl_fns}
${common_fns}" | sort >> "README.md"

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
