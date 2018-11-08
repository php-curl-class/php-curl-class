# Run tests inside container.
command=$(cat <<-END
mkdir --parents "/tmp/php-curl-class" &&
rsync --delete --exclude=".git" --exclude="vendor" --links --recursive "/data/" "/tmp/php-curl-class/" &&
cd "/tmp/php-curl-class" &&
export TRAVIS_PHP_VERSION="5.6" &&
(
    [ ! -f "/tmp/.composer_updated" ] &&
    composer --no-interaction update &&
    touch "/tmp/.composer_updated" ||
    exit 0
) &&
bash "tests/before_script.sh" &&
bash "tests/script.sh"
END
)
docker exec --interactive --tty "php56" sh -c "${command}"
