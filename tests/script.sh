set -x

# Use composer's phpunit and phpcs by adding composer bin directory to the path environment variable.
export PATH="$PWD/vendor/bin:$PATH"

errors=0

# Check syntax in php files. Use `xargs' over `find -exec' as xargs exits with a value of 1 when any command errors.
find . -type "f" -iname "*.php" ! -path "*/vendor/*" | xargs -L "1" php -l
if [[ "${?}" -ne 0 ]]; then
    echo "Error: php syntax checks failed"
    ((errors++))
fi

# Run tests.
phpunit --version
phpunit --configuration "tests/phpunit.xml"
if [[ "${?}" -ne 0 ]]; then
    echo "Error: phpunit command failed"
    ((errors++))
fi

# Enforce line ending consistency in php files.
crlf_file=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec grep --color=always --files-with-matches $'\r' {} \;)
if [[ ! -z "${crlf_file}" ]]; then
    echo "${crlf_file}" | perl -pe 's/(.*)/CRLF line terminators found in \1/'
    ((errors++))
fi

# Enforce indentation character consistency in php files.
tab_char=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec grep --color=always --line-number -H --perl-regexp "\t" {} \;)
if [[ ! -z "${tab_char}" ]]; then
    echo -e "${tab_char}" | perl -pe 's/^(.*)$/Tab character found in \1/'
    ((errors++))
fi

# Enforce indentation consistency in php files.
find_invalid_indentation() {
    filename="${1}"
    script=$(cat <<'EOF'
    $filename = $argv['1'];
    $lines = explode("\n", file_get_contents($filename));
    $line_number = 0;
    foreach ($lines as $line) {
        $line_number += 1;
        $leading_space_count = strspn($line, ' ');
        $remainder = $leading_space_count % 4;
        if (!($remainder === 0)) {
            // Allow doc comments.
            if (substr(ltrim($line), 0, 1) === '*') {
                continue;
            }
            $add_count = 4 - $remainder;
            $remove_count = $remainder;
            echo 'Invalid indentation found in ' . $filename . ':' . $line_number .
                ' (' . $leading_space_count . ':+' . $add_count . '/-' . $remove_count . ')' . "\n";
        }
    }
EOF
)
    php --run "${script}" "${filename}"
}
# Skip hhvm "Notice: File could not be loaded: ..."
if [[ "${TRAVIS_PHP_VERSION}" != "hhvm" ]] && [[ "${TRAVIS_PHP_VERSION}" != "hhvm-nightly" ]]; then
    export -f "find_invalid_indentation"
    invalid_indentation=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec bash -c 'find_invalid_indentation "{}"' \;)
    if [[ ! -z "${invalid_indentation}" ]]; then
        echo "${invalid_indentation}"
        ((errors++))
    fi
fi

# Prohibit trailing whitespace in php files.
trailing_whitespace=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec egrep --color=always --line-number -H " +$" {} \;)
if [[ ! -z "${trailing_whitespace}" ]]; then
    echo -e "${trailing_whitespace}" | perl -pe 's/^(.*)$/Trailing whitespace found in \1/'
    ((errors++))
fi

# Prohibit long lines in php files.
long_lines=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec awk '{print FILENAME":"NR" "length}' {} \; | awk '$2 > 120')
if [[ ! -z "${long_lines}" ]]; then
    echo -e "${long_lines}" | perl -pe 's/^(.*)$/Long lines found in \1/'
    ((errors++))
fi

# Prohibit @author in php files.
at_author=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec egrep --color=always --line-number -H "@author" {} \;)
if [[ ! -z "${at_author}" ]]; then
    echo -e "${at_author}" | perl -pe 's/^(.*)$/\@author found in \1/'
    ((errors++))
fi

# Prohibit screaming caps notation in php files.
caps=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec egrep --color=always --line-number -H -e "FALSE[^']" -e "NULL" -e "TRUE" {} \;)
if [[ ! -z "${caps}" ]]; then
    echo -e "${caps}" | perl -pe 's/^(.*)$/All caps found in \1/'
    ((errors++))
fi

# Require identical comparison operators (===, not ==) in php files.
equal=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec egrep --color=always --line-number -H "[^!=]==[^=]" {} \;)
if [[ ! -z "${equal}" ]]; then
    echo -e "${equal}" | perl -pe 's/^(.*)$/Non-identical comparison operator found in \1/'
    ((errors++))
fi

# Require keyword "elseif" to be used instead of "else if" so that all control keywords look like single words.
elseif=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec egrep --color=always --line-number -H "else\s+if" {} \;)
if [[ ! -z "${elseif}" ]]; then
    echo -e "${elseif}" | perl -pe 's/^(.*)$/Found "else if" instead of "elseif" in \1/'
    ((errors++))
fi

# Require both braces on else statement line; "} else {" and not "}\nelse {".
elses=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec grep --color=always --line-number -H --perl-regexp '^(\s+)?else(\s+)?{' {} \;)
if [[ ! -z "${elses}" ]]; then
    echo -e "${elses}" | perl -pe 's/^(.*)$/Found newline before "else" statement in \1/'
    ((errors++))
fi

# Detect coding standard violations.
phpcs --version
phpcs \
    --extensions="php" \
    --ignore="*/vendor/*" \
    --standard="tests/ruleset.xml" \
    -p \
    -s \
    .
if [[ "${?}" -ne 0 ]]; then
    echo "Error: found standard violation(s)"
    ((errors++))
fi

exit "${errors}"
