# Run tests inside container.
command=$(cat <<-END
mkdir --parents "/tmp/php-curl-class" &&
rsync --delete --exclude=".git" --exclude="vendor" --exclude="composer.lock" --links --recursive "/data/" "/tmp/php-curl-class/" &&
cd "/tmp/php-curl-class" &&
export CI_PHP_VERSION="7.3" &&
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
set -x
docker exec --tty "php73" sh -c "${command}"
