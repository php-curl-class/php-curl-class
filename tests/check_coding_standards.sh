SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${SCRIPT_DIR}"

# Enforce line ending consistency in php files.
crlf_file=$(find .. -type "f" -iname "*.php" ! -path "*/vendor/*" -exec grep --color=always --files-with-matches $'\r' {} \;)
if [[ ! -z "${crlf_file}" ]]; then
    result="$(echo "${crlf_file}" | perl -pe 's/(.*)/CRLF line terminators found in \1/')"
    echo "${result}"
    errors+=("${result}")
fi

# Enforce indentation character consistency in php files.
tab_char=$(find .. -type "f" -iname "*.php" ! -path "*/vendor/*" -exec grep --color=always --line-number -H --perl-regexp "\t" {} \;)
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
invalid_indentation=$(find .. -type "f" -iname "*.php" ! -path "*/vendor/*" -exec bash -c 'find_invalid_indentation "{}"' \;)
if [[ ! -z "${invalid_indentation}" ]]; then
    echo "${invalid_indentation}"
    errors+=("${invalid_indentation}")
fi

# Prohibit trailing whitespace in php files.
trailing_whitespace=$(find .. -type "f" -iname "*.php" ! -path "*/vendor/*" -exec egrep --color=always --line-number -H " +$" {} \;)
if [[ ! -z "${trailing_whitespace}" ]]; then
    result="$(echo -e "${trailing_whitespace}" | perl -pe 's/^(.*)$/Trailing whitespace found in \1/')"
    echo "${result}"
    errors+=("${result}")
fi

# Prohibit long lines in php files.
long_lines=$(find .. -type "f" -iname "*.php" ! -path "*/vendor/*" ! -path "*/www/*" -exec awk '{print FILENAME":"NR" "length}' {} \; | awk '$2 > 120')
if [[ ! -z "${long_lines}" ]]; then
    result="$(echo -e "${long_lines}" | perl -pe 's/^(.*)$/Long lines found in \1/')"
    echo "${result}"
    errors+=("${result}")
fi

# Prohibit @author in php files.
at_author=$(find .. -type "f" -iname "*.php" ! -path "*/vendor/*" -exec egrep --color=always --line-number -H "@author" {} \;)
if [[ ! -z "${at_author}" ]]; then
    result="$(echo -e "${at_author}" | perl -pe 's/^(.*)$/\@author found in \1/')"
    echo "${result}"
    errors+=("${result}")
fi

# Prohibit screaming caps notation in php files.
caps=$(find .. -type "f" -iname "*.php" ! -path "*/vendor/*" -exec egrep --color=always --line-number -H -e "FALSE[^']" -e "NULL" -e "TRUE" {} \;)
if [[ ! -z "${caps}" ]]; then
    result="$(echo -e "${caps}" | perl -pe 's/^(.*)$/All caps found in \1/')"
    echo "${result}"
    errors+=("${result}")
fi

# Require identical comparison operators (===, not ==) in php files.
equal=$(find .. -type "f" -iname "*.php" ! -path "*/vendor/*" -exec egrep --color=always --line-number -H "[^!=]==[^=]" {} \;)
if [[ ! -z "${equal}" ]]; then
    result="$(echo -e "${equal}" | perl -pe 's/^(.*)$/Non-identical comparison operator found in \1/')"
    echo "${result}"
    errors+=("${result}")
fi

# Require keyword "elseif" to be used instead of "else if" so that all control keywords look like single words.
elseif=$(find .. -type "f" -iname "*.php" ! -path "*/vendor/*" -exec egrep --color=always --line-number -H "else\s+if" {} \;)
if [[ ! -z "${elseif}" ]]; then
    result="$(echo -e "${elseif}" | perl -pe 's/^(.*)$/Found "else if" instead of "elseif" in \1/')"
    echo "${result}"
    errors+=("${result}")
fi

# Require both braces on else statement line; "} else {" and not "}\nelse {".
elses=$(find .. -type "f" -iname "*.php" ! -path "*/vendor/*" -exec grep --color=always --line-number -H --perl-regexp '^(\s+)?else(\s+)?{' {} \;)
if [[ ! -z "${elses}" ]]; then
    result="$(echo -e "${elses}" | perl -pe 's/^(.*)$/Found newline before "else" statement in \1/')"
    echo "${result}"
    errors+=("${result}")
fi

# Prohibit use of "is_null" and suggest using the strict comparison operator.
is_null=$(find .. -type "f" -iname "*.php" ! -path "*/vendor/*" -exec grep --color=always --line-number -H -e "is_null" {} \;)
if [[ ! -z "${is_null}" ]]; then
    result="$(echo -e "${is_null}" | perl -pe 's/^(.*)$/is_null found in \1.  Replace with strict comparison (e.g. "\$x === null")./')"
    echo "${result}"
    errors+=("${result}")
fi

# Determine which phpcs to use.
if [[ -f "../vendor/bin/phpcs" ]]; then
    phpcs_to_use="../vendor/bin/phpcs"
else
    phpcs_to_use="phpcs"
fi

# Detect coding standard violations.
"${phpcs_to_use}" --version
"${phpcs_to_use}" \
    --extensions="php" \
    --ignore="*/vendor/*" \
    --standard="ruleset.xml" \
    -p \
    -s \
    ..
if [[ "${?}" -ne 0 ]]; then
    echo "Error: found standard violation(s)"
    errors+=("found standard violation(s)")
fi
