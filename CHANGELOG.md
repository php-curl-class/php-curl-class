# Change Log

PHP Curl Class uses semantic versioning with version numbers written as `MAJOR.MINOR.PATCH`. You may safely update
`MINOR` and `PATCH` version changes. It is recommended to review `MAJOR` changes prior to upgrade as there may be
backwards-incompatible changes that will affect existing usage.

## 9.5.1 - 2021-12-14

### Fixed

- Silence PHP 8.1 deprecations [#691](https://github.com/php-curl-class/php-curl-class/issues/691)
- Remove data parameter from additional request types
  [#689](https://github.com/php-curl-class/php-curl-class/issues/689)

## 9.5.0 - 2021-11-21

### Added

- Method `Curl::setStop()` for stopping requests early without downloading the full response body
  [#681](https://github.com/php-curl-class/php-curl-class/issues/681)

### Fixed

- Fixed constructing request url when using `MultiCurl::addPost()`
  [#686](https://github.com/php-curl-class/php-curl-class/issues/686)

## 9.4.0 - 2021-09-04

### Changed

- Method `Url::parseUrl()` is now public

### Fixed

- Fix parsing schemeless urls [#679](https://github.com/php-curl-class/php-curl-class/issues/679)

## 9.3.1 - 2021-08-05

### Changed

- Enabled strict types (`declare(strict_types=1);`)

### Fixed

- Fixed `Curl::downloadFileName` not being set correctly

## 9.3.0 - 2021-07-23

### Added

- Method `Curl::diagnose()` for troubleshooting requests

## 9.2.0 - 2021-06-23

### Added

- Additional Curl::set\* and MultiCurl::set\* helper methods

    ```
    Curl::setAutoReferer()
    Curl::setAutoReferrer()
    Curl::setFollowLocation()
    Curl::setForbidReuse()
    Curl::setMaximumRedirects()
    MultiCurl::setAutoReferer()
    MultiCurl::setAutoReferrer()
    MultiCurl::setFollowLocation()
    MultiCurl::setForbidReuse()
    MultiCurl::setMaximumRedirects()
    ```

### Fixed

- Closing curl handles [#670](https://github.com/php-curl-class/php-curl-class/issues/670)
- Use of "$this" in non-object context [#671](https://github.com/php-curl-class/php-curl-class/pull/671)

## 9.1.0 - 2021-03-24

### Added

- Support for using relative urls with MultiCurl::add\*() methods [#628](https://github.com/php-curl-class/php-curl-class/issues/628)

## 9.0.0 - 2021-03-19

### Changed

- Use short array syntax

### Removed

- Support for PHP 5.3, 5.4, 5.5, and 5.6 [#380](https://github.com/php-curl-class/php-curl-class/issues/380)

## Manual Review

A manual review of changes is possible using the
[comparison page](https://github.com/php-curl-class/php-curl-class/compare/). For example, visit
[7.4.0...8.0.0](https://github.com/php-curl-class/php-curl-class/compare/7.4.0...8.0.0) to compare the changes for
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
