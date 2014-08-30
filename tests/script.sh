find . -type "f" -iname "*.php" | xargs -L "1" php -l
cd tests && phpunit --configuration phpunit.xml
