# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.0.6] - 26 November 2026
### Fixed
- Admin users without this module's permissions could still load and act on the EAV entity type grid through the Loki AJAX endpoint, because the grid fell back to the generic `Loki_Components::index` resource; the grid now enforces its own ACL resources (grid, save, delete) nested under Content > Elements, and the menu item points to a declared resource
- EAV entity types could still be changed through inline editing in a grid meant to be read-only (`hide_actions` only hid the buttons); inline edit updates are now rejected server-side
- Missing dependencies: `Loki_AdminComponents` 0.9.1 or newer is now required and `Magento_Eav` is declared as a dependency
- The grid page controller now only accepts GET requests

### Changed
- Supported PHP versions are now 8.2 up to 8.5 (PHP 8.1 support dropped, PHP 8.5 support added)

## [0.0.5] - 27 September 2025
### Fixed
- Loosen constraints with Loki Admin Components

## [0.0.4] - 17 September 2025
### Fixed
- Allow setting visible flag on grid column
- Change flag allow_actions into hide_actions

## [0.0.3] - 02 September 2025
### Fixed
- Add CI files

## [0.0.2] - 24 July 2025
### Fixed
- Update deps with Loki packages
- Use `Loki_AdminComponents` namespace
- Update README.md
- Add files via upload
- Move URL config to XML layout
- Update instructions with `@dev`
- Remove unneeded layout arguments

## [0.0.1] - 16 May 2025
### Added
- Initial release
