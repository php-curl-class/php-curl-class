# Testing

### Run Tests Locally

To run tests:

```bash
git clone https://github.com/php-curl-class/php-curl-class.git
cd php-curl-class/
composer update
./tests/run.sh
```

To run select tests:

```bash
git clone https://github.com/php-curl-class/php-curl-class.git
cd php-curl-class/
composer update
./tests/run.sh --filter=keyword
```

To test all PHP versions in containers:

```bash
git clone https://github.com/php-curl-class/php-curl-class.git
cd php-curl-class/
./tests/test_all.sh
```

### Continuous Integration Tests

Continuous integration runs [tests/ci.sh](https://github.com/php-curl-class/php-curl-class/blob/master/tests/ci.sh) on supported PHP versions and is configured with [.github/workflows/ci.yml](https://github.com/php-curl-class/php-curl-class/blob/master/.github/workflows/ci.yml).
