# Change Log

PHP Curl Class uses semantic versioning with version numbers written as `MAJOR.MINOR.PATCH`. You may safely update
`MINOR` and `PATCH` version changes. It is recommended to review `MAJOR` changes prior to upgrade as there may be
backwards-incompatible changes that will affect existing usage.

<!-- CHANGELOG_PLACEHOLDER -->

## 9.14.3 - 2023-03-13

- Remove use of array_merge() inside loop ([#774](https://github.com/php-curl-class/php-curl-class/pull/774))

## 9.14.2 - 2023-03-09

- Clean up: Reduce nesting ([#771](https://github.com/php-curl-class/php-curl-class/pull/771))

## 9.14.1 - 2023-02-27

- Remove coding standard ruleset exclusion ([#768](https://github.com/php-curl-class/php-curl-class/pull/768))

## 9.14.0 - 2023-02-26

- Make https:// and http:// the allowed request protocols by default ([#767](https://github.com/php-curl-class/php-curl-class/pull/767))

## 9.13.1 - 2023-01-16

- Allow uploads with CURLStringFile type  ([#762](https://github.com/php-curl-class/php-curl-class/pull/762))

## 9.13.0 - 2023-01-13

- Implement abstract class BaseCurl for Curl and MultiCurl ([#759](https://github.com/php-curl-class/php-curl-class/pull/759))
- Display error messages found in Curl::diagnose() ([#758](https://github.com/php-curl-class/php-curl-class/pull/758))
- Fix Curl::diagnose() request type output for POST requests ([#757](https://github.com/php-curl-class/php-curl-class/pull/757))

## 9.12.6 - 2023-01-11

- Replace use of #[\AllowDynamicProperties] ([#756](https://github.com/php-curl-class/php-curl-class/pull/756))
- silence PHP 8.2 deprecation notices ([#754](https://github.com/php-curl-class/php-curl-class/pull/754))

## 9.12.5 - 2022-12-20

- Fix static analysis error ([#752](https://github.com/php-curl-class/php-curl-class/pull/752))

## 9.12.4 - 2022-12-17

- Exclude additional files from git archive ([#751](https://github.com/php-curl-class/php-curl-class/pull/751))

## 9.12.3 - 2022-12-13

- Ensure string response before gzip decode ([#749](https://github.com/php-curl-class/php-curl-class/pull/749))

## 9.12.2 - 2022-12-11

- Disable warning when gzip-decoding response errors ([#748](https://github.com/php-curl-class/php-curl-class/pull/748))

## 9.12.1 - 2022-12-08

- Include option constant that uses the CURLINFO_ prefix ([#745](https://github.com/php-curl-class/php-curl-class/pull/745))

## 9.12.0 - 2022-12-07

- Add automatic gzip decoding of response ([#744](https://github.com/php-curl-class/php-curl-class/pull/744))

## 9.11.1 - 2022-12-06

- change: remove unused namespace import ([#743](https://github.com/php-curl-class/php-curl-class/pull/743))

## 9.11.0 - 2022-12-05

- Add Curl::diagnose() HTTP method check matches methods allowed ([#741](https://github.com/php-curl-class/php-curl-class/pull/741))
- Add temporary fix missing template params ([#742](https://github.com/php-curl-class/php-curl-class/pull/742))

## 9.10.0 - 2022-11-07

- Display request options in Curl::diagnose() output ([#739](https://github.com/php-curl-class/php-curl-class/pull/739))

## 9.9.0 - 2022-11-06

- Fix MultiCurl::setCookieString() ([#738](https://github.com/php-curl-class/php-curl-class/pull/738))
- Pass MultiCurl options to new Curl instances earlier ([#737](https://github.com/php-curl-class/php-curl-class/pull/737))
- Add deferred constant curlErrorCodeConstants ([#736](https://github.com/php-curl-class/php-curl-class/pull/736))

## 9.8.0 - 2022-10-01

- Include curl error code constant in curl error message ([#733](https://github.com/php-curl-class/php-curl-class/pull/733))

## 9.7.0 - 2022-09-29

- Implement ArrayUtil::arrayRandomIndex() ([#732](https://github.com/php-curl-class/php-curl-class/pull/732))

## 9.6.3 - 2022-09-24

- Remove filter flag constants deprecated as of PHP 7.3 ([#730](https://github.com/php-curl-class/php-curl-class/pull/730))

## 9.6.2 - 2022-09-24

- Call MultiCurl::beforeSend() before each request is made ([#723](https://github.com/php-curl-class/php-curl-class/pull/723))
- Encode keys for post data with numeric keys ([#726](https://github.com/php-curl-class/php-curl-class/pull/726))
- Fix building post data with object ([#728](https://github.com/php-curl-class/php-curl-class/pull/728))

## 9.6.1 - 2022-06-30

### Fixed

- Attempt to stop active requests when `MultiCurl::stop()` is called
  [#714](https://github.com/php-curl-class/php-curl-class/issues/714)
  [#718](https://github.com/php-curl-class/php-curl-class/issues/718)
- Retain keys for arrays with null values when building post data
  [#712](https://github.com/php-curl-class/php-curl-class/issues/712)

## 9.6.0 - 2022-03-17

### Added

- Method `MultiCurl::stop()` for stopping subsequent requests
  [#708](https://github.com/php-curl-class/php-curl-class/issues/708)

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
