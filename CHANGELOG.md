# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [Unreleased]
- Change the popup (to see hook informations) to another component (in header or footer ?) and to be able to switch between them


## [1.3.0] - 2021-04-08
### Added
- Ignore action and filter hooks to avoid breaking operations

### Changed
- Checking of the administration directory according to the Prestashop constant, instead of starting from the a-priori that all the administrations start with `/ admin`
- Readme file translated into English, for all non-French friends
- Changelog format based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
- Display all parameters (i.e. `smarty`, `cookie` or `cart`) in the hook informations

### Removed
- Zip file is no longer useful, please download module from Github releases instead



## [1.2.0] - 2021-03-19
### Added
- Awesome popup to see details on hooks
- Restrict if debug mode is enabled
- Reset module position to be in first for all hooks
- Hide and display hooks from console

### Changed
- Way to retrieve module config fields
- Refactoring and improvements

### Removed
- Configuration fields example


## [1.1.1] - 2021-03-17
### Fixed
- Restriction by IP


## [1.1.0] - 2021-03-16
### Added
- Possibility that only logged in admins see hooks
- CSS on displayed hooks

### Changed
- Automatic hook install
- Translations
- Some typos, refactoring and improvements

### Removed
- Manual hook installation (from `generate_hooks.php` file)


## [1.0.0] - 2021-02-24
Initial release
