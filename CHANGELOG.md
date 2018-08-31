# Changelog

## [1.0.0] - 2018-08-28
### Added
- Pusher broadcasting
- Ability to disble pooling
### Breaking Changes
- Add PUSHER_CLUSTER=<cluster name> to your .env file
- Update your root.yml according to config/tddd/root.yml, regarding the pooling key

## [0.9.9] - 2017-12-08
### Added
- Toggle enabled/disabled projects

## [0.9.8] - 2017-12-08
### Added
- Always display log show button

## [0.9.7] - 2017-12-07
### Changed
- Removed clear artisan command

## [0.9.6] - 2017-12-07
### Added
- Tests coverage tab on tests log view
### Changed
- Config files are now in yaml format
- Renamed package to TDDD - Test Driven Development Dashboard

## [0.9.5] - 2017-10-16
### Added
- MySQL Support
- Option to add environment variables to tester script
- Config for different editors and a default editor
- User can now set one different editor for each suite
- Config for PHPStorm editor
- Config for Sublime Text 3 editor
- Config for Visual Studio Code editor
- Option to disable project on Dashboard
- Projects can now be disbled
- No need to refresh page when rebooting watcher anymore
- Input to filter projects
- Option to configure the poll interval (defaults to 1500ms)
- Show spinner on running project
- Show badge (passed/failed) for each project
- Button to run all tests on all (filtered) projects
- Added AVA tester
- Test state to log modal
- Option to run test from the log modal
- Option to reset state of all projects
- Watch and automatically reload config
- Display tester log in real-time
### Changed
- Allow better configuration of editor's binary
- Moved Laravel related classes out from Vendor\Laravel
- Completely restructure package directory
- License is now MIT
- Improved regex matcher of editable source files (and lines)
### Fixed
- Abending when tester used in suite does not exists
- Piper script not being 
- Test subfolders not stored correctly 

## [0.9.4] - 2017-10-11
### Added
- Show Jest snapshots in dashboard log modal
- Show exclustions in terminal log

## [0.9.3] - 2017-10-10
### Added
- Support for Javascript testing (Jest)
### Changed
- Ignore abstract PHP classes

## [0.9.2] - 2017-09-10
### Changed
- Bug fixes

## [0.9.1] - 2017-08-10
### Changed
- Bug fixes

## [0.9.0] - 2017-07-10
### Changed
- Complete redesign of dashboard
- Moved from ReactJS to VueJS

## [0.5.0] - 2015-03-10
### Added
- Support Laravel 5

## [0.1.0] - 2014-07-06
### Added
- First version
