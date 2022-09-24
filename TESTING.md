# Testing

### Run Tests

To run tests:

    $ git clone https://github.com/php-curl-class/php-curl-class.git
    $ cd php-curl-class/
    $ composer update
    $ ./tests/run.sh

To run select tests:

    $ git clone https://github.com/php-curl-class/php-curl-class.git
    $ cd php-curl-class/
    $ composer update
    $ ./tests/run.sh --filter=keyword

To test all PHP versions in containers:

    $ git clone https://github.com/php-curl-class/php-curl-class.git
    $ cd php-curl-class/
    $ ./tests/test_all.sh
