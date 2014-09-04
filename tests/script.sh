find . -type "f" -iname "*.php" | xargs -L "1" php -l

trailing_whitespace=$(find . -type "f" -iname "*.php" -exec egrep --line-number -H " +$" {} \;)
if [[ ! -z "${trailing_whitespace}" ]]; then
    echo -e "${trailing_whitespace}" | perl -pe 's/^(.*)$/Trailing whitespace found in \1/'
    exit 1
fi

cd tests && phpunit --configuration phpunit.xml
