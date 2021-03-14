# Change Log

PHP Curl Class uses semantic versioning with version numbers written as `MAJOR.MINOR.PATCH`. You may safely update
`MINOR` and `PATCH` version changes. It is recommended to review `MAJOR` changes prior to upgrade as there may be
backwards-incompatible changes that will affect existing usage.

### Changes

(TODO: Add changes for next `MAJOR` version release.)

### Manual Review

Manually view changes on the [comparison page](https://github.com/php-curl-class/php-curl-class/compare/). For example,
visit [7.4.0...8.0.0](https://github.com/php-curl-class/php-curl-class/compare/7.4.0...8.0.0) to compare the changes for
the `MAJOR` upgrade from 7.4.0 to 8.0.0. Comparing against `HEAD` is also possible using the `tag...HEAD` syntax
([8.3.0...HEAD](https://github.com/php-curl-class/php-curl-class/compare/8.3.0...HEAD)).

View the log between releases:

    $ git fetch --tags
    $ git log 7.4.0...8.0.0

View the code changes between releases:

    $ git fetch --tags
    $ git diff 7.4.0...8.0.0

View only the source log and code changes between releases:

    $ git log 7.4.0...8.0.0 "src/"
    $ git diff 7.4.0...8.0.0 "src/"

View only the source log and code changes between a release and the current checked-out commit:

    $ git log 8.0.0...head "src/"
    $ git diff 8.0.0...head "src/"
