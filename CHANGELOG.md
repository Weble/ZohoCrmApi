# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [5.2.0]
- Added support for illuminate collections v9
- Added tests for php 8.1
- Allow psr/cache next versions

## [5.1.1]

### Fixed
- Fix pagination issue

## [5.1.0]

### Added

- Added methods to upload and download **Attachments** of a record
- Added methods to list **Notes** of a record

## [5.0.0]

### Added

- Added changelog file

### Fixed

### Changed

- Updated `ZohoClient` dependency to v4
- Moved any use of `spatie/enum` to normal `const`

### Removed

- Removed `spatie/enum` dependency to lower the list of dependencies
- Removed the `throttle` option. You can pass in a custom http client implementation with a throttle middleware if you need this feature (example: a custom guzzle client with [https://github.com/caseyamcl/guzzle_retry_middleware](https://github.com/caseyamcl/guzzle_retry_middleware))


## [4.0.2] - 2020-10-19

[unreleased]: https://github.com/olivierlacan/keep-a-changelog/compare/v4.0.2...HEAD
[4.0.2]: https://github.com/weble/zohocrmapi/compare/v4.0.0...v4.0.2
[4.0.1]: https://github.com/weble/zohocrmapi/compare/v4.0.0...v4.0.1
[4.0.0]: https://github.com/weble/zohocrmapi/compare/v4.0.0...v4.0.0
