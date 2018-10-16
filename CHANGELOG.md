# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## v5.0.0 - 2018.10.16
### Added
- Support for PHP 7.x

### Removed
- Support for PHP 5.x

### Changed
- Library was fully refactored

## v4.1.2 - 2016.03.20
### Added
- Nothing

### Removed
- Nothing

### Changed
- Nothing

### Fixed
- Fixed a bug in `Route::getCompiler` method

## v4.1.1 - 2016.01.20
### Added
- Added an optional parameter to `Opis\View\EngineResolver::resolve` method and to the
`Opis\View\EngineEntry::instance` method.
- Added a new protected `param` property. The value of this property will be used as an argument when calling
the `Opis\View\EngineResolver::resolve` method

### Removed
- Nothing

### Changed
- Nothing

### Fixed
- Nothing

## v4.1.0 - 2016.01.16
### Added
- Nothing

### Removed
- Nothing

### Changed
- Updated `opis/routing` library dependency to version `^4.1.0`
- Modified `UserFilter` class to reflect changes

### Fixed
- Fixed a bug caused by the `uasort` function which behaves different in PHP 7 than it does in P

## v4.0.0 - 2016.01.15
### Added
- Tests

### Removed
- Nothing

### Changed
- Updated `opis/routing` library dependency to version `4.0.*`
- Updated `UserFilter` class in order to reflect changes made in `opis/routing` library

### Fixed
- CS

## v3.1.0 - 2015.08.30
### Added
- `resolveViewName` method in `ViewRouter` class

### Removed
- `branch-alias` property from `composer.json` file

### Changed
- View names are now cached.

### Fixed
- Nothing


## v3.0.0 - 2015.07.31
### Added
- Autoload file
- Support for custom filtering

### Removed
- Nothing

### Changed
- Updated `opis/routing` library dependency to version `3.0.*`

### Fixed
- Fixed a small bug in `EngineResover::unserialize`
- Fixed a bug in `ViewRouter`'s `renderView` method

## v2.5.0 - 2015.03.20
### Added
- Nothing

### Removed
- Nothing

### Changed
- Updated `opis/routing` library dependency to version `2.5.*`

### Fixed
- Nothing

## v2.4.0 - 2014.10.23
### Added
- Nothing

### Removed
- Nothing

### Changed
- Updated `opis/routing` library dependency to version `2.4.*`

### Fixed
- Nothing

## v2.3.1 - 2014.06.11
### Added
- Nothing

### Removed
- Nothing

### Changed
- Nothing

### Fixed
- Fixed a bug in `Opis\View\Route`.

## v2.3.0 - 2014.06.11
### Added
- Nothing

### Removed
- Nothing

### Changed
- Updated `opis/routing` library dependency to version `2.3.0`
- Updated `Opis\View\Route` to reflect changes that were made in `opis/routing`

### Fixed
- Nothing

## v2.2.0 - 2014.06.04
### Added
- Changelog

### Removed
- Nothing

### Changed
- Updated `opis/routing` dependency to version `2.2.*

### Fixed
- Nothing
