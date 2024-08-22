SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

# Run commands from the project root directory.
cd ..

# Enforce line ending consistency in php files.
crlf_file=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec grep --color=always --files-with-matches $'\r' {} \;)
if [[ ! -z "${crlf_file}" ]]; then
    result="$(echo "${crlf_file}" | perl -pe 's/(.*)/CRLF line terminators found in \1/')"
    echo "${result}"
    errors+=("${result}")
fi

# Enforce indentation character consistency in php files.
tab_char=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec grep --color=always --line-number -H --perl-regexp "\t" {} \;)
if [[ ! -z "${tab_char}" ]]; then
    result="$(echo -e "${tab_char}" | perl -pe 's/^(.*)$/Tab character found in \1/')"
    echo "${result}"
    errors+=("${result}")
fi

# Enforce indentation consistency in php files.
find_invalid_indentation() {
    filename="${1}"
    script=$(cat <<'EOF'
    $file_name_color   = '35'; // 35 = Magenta.
    $separator_color   = '36'; // 36 = Cyan.
    $line_number_color = '32'; // 32 = Green.

    $filename = $argv['1'];
    $lines = explode("\n", file_get_contents($filename));
    $line_number = 0;
    foreach ($lines as $line) {
        $line_number += 1;
        $leading_space_count = strspn($line, ' ');
        $remainder = $leading_space_count % 4;
        if ($remainder !== 0) {
            $trimmed_line = ltrim($line);

            // Allow doc comments.
            if (substr($trimmed_line, 0, 1) === '*') {
                continue;
            }

            // Allow method chaining.
            if (substr($trimmed_line, 0, 2) === '->') {
                continue;
            }

            $add_count = 4 - $remainder;
            $remove_count = $remainder;
            echo 'Invalid indentation found in ' .
                "\033[" . $file_name_color   . 'm' . $filename    . "\033[0m" .
                "\033[" . $separator_color   . 'm' . ':'          . "\033[0m" .
                "\033[" . $line_number_color . 'm' . $line_number . "\033[0m" .
                ' (' . $leading_space_count . ':+' . $add_count . '/-' . $remove_count . ')' . "\n";
        }
    }
EOF
)
    php --run "${script}" "${filename}"
}
export -f "find_invalid_indentation"
invalid_indentation=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec bash -c 'find_invalid_indentation "{}"' \;)
if [[ ! -z "${invalid_indentation}" ]]; then
    echo "${invalid_indentation}"
    errors+=("${invalid_indentation}")
fi

# Prohibit trailing whitespace in php files.
trailing_whitespace=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec grep --color=always --extended-regexp --line-number -H " +$" {} \;)
if [[ ! -z "${trailing_whitespace}" ]]; then
    result="$(echo -e "${trailing_whitespace}" | perl -pe 's/^(.*)$/Trailing whitespace found in \1/')"
    echo "${result}"
    errors+=("${result}")
fi

# Require identical comparison operators (===, not ==) in php files.
equal=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec grep --color=always --extended-regexp --line-number -H "[^!=]==[^=]" {} \;)
if [[ ! -z "${equal}" ]]; then
    result="$(echo -e "${equal}" | perl -pe 's/^(.*)$/Non-identical comparison operator found in \1/')"
    echo "${result}"
    errors+=("${result}")
fi

# Require both braces on else statement line; "} else {" and not "}\nelse {".
elses=$(find . -type "f" -iname "*.php" ! -path "*/vendor/*" -exec grep --color=always --line-number -H --perl-regexp '^(\s+)?else(\s+)?{' {} \;)
if [[ ! -z "${elses}" ]]; then
    result="$(echo -e "${elses}" | perl -pe 's/^(.*)$/Found newline before "else" statement in \1/')"
    echo "${result}"
    errors+=("${result}")
fi

# Run PHP_CodeSniffer.
# Determine which phpcs to use.
if [[ -f "vendor/bin/phpcs" ]]; then
    phpcs_to_use="vendor/bin/phpcs"
else
    phpcs_to_use="phpcs"
fi

# Detect coding standard violations.
"${phpcs_to_use}" --version
"${phpcs_to_use}" \
    --extensions="php" \
    --ignore="*/vendor/*" \
    --standard="tests/ruleset.xml" \
    -p \
    -s \
    .
if [[ "${?}" -ne 0 ]]; then
    echo "Error: found PHP_CodeSniffer coding standard violation(s)"
    errors+=("found PHP_CodeSniffer coding standard violation(s)")
fi

# Run PHP-CS-Fixer.
if   [[ "${CI_PHP_VERSION}" == "7.3" ]]; then :
else
    vendor/bin/php-cs-fixer --version
    vendor/bin/php-cs-fixer fix --ansi --config="tests/.php-cs-fixer.php" --diff --dry-run
    if [[ "${?}" -ne 0 ]]; then
        echo "Error: found PHP-CS-Fixer coding standard violation(s)"
        errors+=("found PHP-CS-Fixer coding standard violation(s)")
    fi
fi
