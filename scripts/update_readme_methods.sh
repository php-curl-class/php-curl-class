before=$(head -n $(
    grep --context="0" --line-number --max-count="1" "### Available Methods" "README.md" |
    perl -pe 's/^(\d+):.*/\1/') "README.md")

after=$(tail -n +$(
    grep --context="0" --line-number --max-count="1" "### Contribute" "README.md" |
    perl -pe 's/^(\d+):.*/\1/') "README.md")

echo "${before}" > "README.md"

curl_max_line_number=$(grep --context="0" --line-number --max-count="1" '^}$' "src/Curl/Curl.php" | \
    perl -pe 's/^(\d+):.*/\1/')
echo '```php' >> "README.md"
head -n "${curl_max_line_number}" "src/Curl/Curl.php" | \
    egrep "^    .* function .*" | \
    egrep "^    public" | \
    sort | \
    perl -pe 's/^    public (.* )?function /Curl::/' | \
    tee -a "README.md"
egrep "^    .* function .*" "src/Curl/MultiCurl.php" | \
    egrep "^    public" | \
    sort | \
    perl -pe 's/^    public (.* )?function /MultiCurl::/' | \
    tee -a "README.md"
echo '```' >> "README.md"
echo >> "README.md"

echo "${after}" >> "README.md"
