# Changelog

All notable changes to `nativephp-laravel` will be documented in this file.

## 1.0.0-beta.1 - 2025-01-21

### What's Changed

* Remove queue worker initialization by @XbNz in https://github.com/NativePHP/electron/pull/149
* close print window before freeing mem by @labomatik in https://github.com/NativePHP/electron/pull/146
* fix: all test suites by @SRWieZ in https://github.com/NativePHP/electron/pull/148
* Fix menubar not ready by @SRWieZ in https://github.com/NativePHP/electron/pull/150
* Add support for window.show() by @curtisblackwell in https://github.com/NativePHP/electron/pull/151
* chore: cleaning + major versions upgrade  by @SRWieZ in https://github.com/NativePHP/electron/pull/147
* fix: menubar tray state by @SRWieZ in https://github.com/NativePHP/electron/pull/156
* Add Windows support and deep linking configuration by @JulianaChiabai in https://github.com/NativePHP/electron/pull/158
* fix: kill tree php server by @SRWieZ in https://github.com/NativePHP/electron/pull/157

### New Contributors

* @XbNz made their first contribution in https://github.com/NativePHP/electron/pull/149
* @labomatik made their first contribution in https://github.com/NativePHP/electron/pull/146
* @curtisblackwell made their first contribution in https://github.com/NativePHP/electron/pull/151
* @JulianaChiabai made their first contribution in https://github.com/NativePHP/electron/pull/158

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.9.0...0.10.0

## 0.9.0 - 2024-12-19

### What's Changed

* Fix script identity by @simonhamp in https://github.com/NativePHP/electron/pull/136
* feat: add clear endpoint to settings by @SRWieZ in https://github.com/NativePHP/electron/pull/141
* Dock goodies by @simonhamp in https://github.com/NativePHP/electron/pull/138
* MenuBars continued by @simonhamp in https://github.com/NativePHP/electron/pull/137
* Menu improvements by @simonhamp in https://github.com/NativePHP/electron/pull/139
* Feature/context menu helper by @gwleuverink in https://github.com/NativePHP/electron/pull/140
* Cleanup by @simonhamp in https://github.com/NativePHP/electron/pull/142
* latest.yml overwritten when building multiple architectures by @gwleuverink in https://github.com/NativePHP/electron/pull/145
* Fixes and improvements to powerMonitor by @SRWieZ in https://github.com/NativePHP/electron/pull/143

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.8.7...0.9.0

## 0.8.7 - 2024-11-17

### What's Changed

* Fix/forward native env variables to Child Processes by @gwleuverink in https://github.com/NativePHP/electron/pull/129
* Fix some MenuBar quirks by @simonhamp in https://github.com/NativePHP/electron/pull/133
* Pass MenuBar clicked event parameters correctly by @simonhamp in https://github.com/NativePHP/electron/pull/134
* Delete the `native:queue` command by @JustinLawrenceMS in https://github.com/NativePHP/electron/pull/130

### New Contributors

* @JustinLawrenceMS made their first contribution in https://github.com/NativePHP/electron/pull/130

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.8.6...0.8.7

## 0.8.6 - 2024-11-14

### What's Changed

* MenuBar improvements by @simonhamp in https://github.com/NativePHP/electron/pull/131

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.8.5...0.8.6

## 0.8.5 - 2024-11-13

### What's Changed

* Tidy / remove commented out child process env merge by @gwleuverink in https://github.com/NativePHP/electron/pull/126
* Create build-plugin.yml by @simonhamp in https://github.com/NativePHP/electron/pull/127
* Make the queue worker work by @simonhamp in https://github.com/NativePHP/electron/pull/128

### New Contributors

* @gwleuverink made their first contribution in https://github.com/NativePHP/electron/pull/126

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.8.4...0.8.5

## 0.8.4 - 2024-11-11

### What's Changed

* Fix the relative path to PHP by @JCombee in https://github.com/NativePHP/electron/pull/125
* Improve child process by @SRWieZ in https://github.com/NativePHP/electron/pull/123
* Fix powerMonitor events by @SRWieZ in https://github.com/NativePHP/electron/pull/122

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.8.3...0.8.4

## 0.8.3 - 2024-11-05

Hotfix

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.8.2...0.8.3

## 0.8.2 - 2024-11-05

### `composer native:dev` 🔥

To get the command, run `php artisan native:install` after updating to this version

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.8.1...0.8.2

## 0.8.1 - 2024-11-04

### What's Changed

* Add endpoint for listing all windows by @JCombee in https://github.com/NativePHP/electron/pull/120
* Add PHP server exception reporting by @simonhamp in https://github.com/NativePHP/electron/pull/121

### New Contributors

* @JCombee made their first contribution in https://github.com/NativePHP/electron/pull/120

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.8.0...0.8.1

## 0.8.0 - 2024-11-01

### What's Changed

* Integrate Plugin by @RobertWesner in https://github.com/NativePHP/electron/pull/109
* Add active screen endpoint by @simonhamp in https://github.com/NativePHP/electron/pull/112
* Support `--no-interaction` in `native:serve` command by @simonhamp in https://github.com/NativePHP/electron/pull/119
* Child processes by @simonhamp in https://github.com/NativePHP/electron/pull/115
* Improved window management by @simonhamp in https://github.com/NativePHP/electron/pull/118
* Add support for custom events fired by clicking on notifications by @LukeTowers in https://github.com/NativePHP/electron/pull/114
* Fix windows php path for native:minify by @kritish-dhaubanjar in https://github.com/NativePHP/electron/pull/111
* Bump actions/checkout from 2 to 4 by @dependabot in https://github.com/NativePHP/electron/pull/116
* Bump actions/setup-node from 1 to 4 by @dependabot in https://github.com/NativePHP/electron/pull/117

### New Contributors

* @LukeTowers made their first contribution in https://github.com/NativePHP/electron/pull/114
* @kritish-dhaubanjar made their first contribution in https://github.com/NativePHP/electron/pull/111

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.7.4...0.8.0

## 0.7.4 - 2024-10-16

### What's Changed

* Fix for Laravel Prompts version by @sarukomine in https://github.com/NativePHP/electron/pull/110

### New Contributors

* @sarukomine made their first contribution in https://github.com/NativePHP/electron/pull/110

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.7.3...0.7.4

## 0.7.3 - 2024-09-27

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.7.2...0.7.3

## 0.7.2 - 2024-09-09

### What's Changed

* Updated php-bin dependencies to ^0.5 by @SRWieZ in https://github.com/NativePHP/electron/pull/107
* Fixed Tests by @RobertWesner in https://github.com/NativePHP/electron/pull/104
* Customizable deeplink scheme by @simonhamp in https://github.com/NativePHP/electron/pull/105
* Add environment variable for php bin directory. by @kisuka in https://github.com/NativePHP/electron/pull/101

### New Contributors

* @SRWieZ made their first contribution in https://github.com/NativePHP/electron/pull/107
* @RobertWesner made their first contribution in https://github.com/NativePHP/electron/pull/104
* @kisuka made their first contribution in https://github.com/NativePHP/electron/pull/101

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.7.1...0.7.2

## 0.7.1 - 2024-09-02

### What's Changed

* App Icon version 2 by @simonhamp in https://github.com/NativePHP/electron/pull/103

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.7.0...0.7.1

## 0.7.0 - 2024-08-06

Update the NPM dependencies

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.6.5...0.7.0

## 0.6.5 - 2024-07-23

### What's Changed

* fix: add missing platform.arch for Linux and Windows by @martinpl in https://github.com/NativePHP/electron/pull/100

### New Contributors

* @martinpl made their first contribution in https://github.com/NativePHP/electron/pull/100

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.6.4...0.6.5

## 0.6.4 - 2024-07-18

### What's Changed

* Remove forced config from Electron config by @simonhamp in https://github.com/NativePHP/electron/pull/95
* Patch app name during development by @simonhamp in https://github.com/NativePHP/electron/pull/96
* Bump dependabot/fetch-metadata from 2.1.0 to 2.2.0 by @dependabot in https://github.com/NativePHP/electron/pull/99
* Fixed missing import of select() function from Laravel Prompts by @rinodrummer in https://github.com/NativePHP/electron/pull/98
* Fix to issue nativephp/laravel #318 + General cleanup and DRYied Publish command by @Rudeisnice in https://github.com/NativePHP/electron/pull/97

### New Contributors

* @rinodrummer made their first contribution in https://github.com/NativePHP/electron/pull/98

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.6.3...0.6.4

## 0.6.3 - 2024-05-06

### What's Changed

* Use Prompts to request Platform when not provided by @vikas5914 in https://github.com/NativePHP/electron/pull/92
* Fix publish command by @simonhamp in https://github.com/NativePHP/electron/pull/93
* Fix `native:publish mac` by @simonhamp in https://github.com/NativePHP/electron/pull/94

### New Contributors

* @vikas5914 made their first contribution in https://github.com/NativePHP/electron/pull/92

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.6.2...0.6.3

## 0.6.2 - 2024-05-02

### What's Changed

* Fix the `native:minify` command run during build (inspired by @KajPe's [patch](https://github.com/NativePHP/laravel/issues/277#issuecomment-2088910160))

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.6.1...0.6.2

## 0.6.1 - 2024-05-01

### What's Changed

- Fix PHP executable loading on Windows

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.6.0...0.6.1

## 0.6.0 - 2024-04-30

### What's Changed

* Uses the latest version of nativephp/php-bin and correctly selects the appropriate binary when building your app 🎉
* Fixes NativePHP/laravel#244 which was a stumbling block for installing on Windows
* Add default Amazon S3 auto-update provider config by @lonnylot in https://github.com/NativePHP/electron/pull/65
* Upgrade Testbench by @crynobone in https://github.com/NativePHP/electron/pull/87
* Bump dependabot/fetch-metadata from 2.0.0 to 2.1.0 by @dependabot in https://github.com/NativePHP/electron/pull/91

### New Contributors

* @crynobone made their first contribution in https://github.com/NativePHP/electron/pull/87

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.5.1...0.6.0

## 0.5.1 - 2024-04-28

### What's Changed

* Updated the electron-builder dependency constraint to the latest
* Added prep code for upcoming new PHP binaries
* Bump dependabot/fetch-metadata from 1.6.0 to 2.0.0 by @dependabot in https://github.com/NativePHP/electron/pull/88
* Bump ramsey/composer-install from 2 to 3 by @dependabot in https://github.com/NativePHP/electron/pull/85
* Fix undefined class Process in ExampleTest.php by @LunashaGit in https://github.com/NativePHP/electron/pull/83
* add space between cross-env and node by @A3Brothers in https://github.com/NativePHP/electron/pull/81
* Add the return types for each methods by @LunashaGit in https://github.com/NativePHP/electron/pull/84
* Bump aglipanci/laravel-pint-action from 2.3.1 to 2.4 by @dependabot in https://github.com/NativePHP/electron/pull/90
* allow crossbuilding + minor DRY cleanup by @Rudeisnice in https://github.com/NativePHP/electron/pull/79

### New Contributors

* @LunashaGit made their first contribution in https://github.com/NativePHP/electron/pull/83
* @Rudeisnice made their first contribution in https://github.com/NativePHP/electron/pull/79

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.5.0...0.5.1

## 0.5.0 - 2024-04-01

### What's Changed

* Laravel 11 support by @meliani in https://github.com/NativePHP/electron/pull/89
* Allow compiling packages in a CI environment by @danjohnson95 in https://github.com/NativePHP/electron/pull/74
* Upload to GitHub Releases by @danjohnson95 in https://github.com/NativePHP/electron/pull/75
* Windows flag for electron builder. by @A3Brothers in https://github.com/NativePHP/electron/pull/77
* Automate platform detection by @kpanuragh in https://github.com/NativePHP/electron/pull/52
* Fix broken links in README by @danjohnson95 in https://github.com/NativePHP/electron/pull/73
* Bump actions/checkout from 3 to 4 by @dependabot in https://github.com/NativePHP/electron/pull/78
* Bump stefanzweifel/git-auto-commit-action from 4 to 5 by @dependabot in https://github.com/NativePHP/electron/pull/80
* Bump aglipanci/laravel-pint-action from 2.3.0 to 2.3.1 by @dependabot in https://github.com/NativePHP/electron/pull/82

### New Contributors

* @danjohnson95 made their first contribution in https://github.com/NativePHP/electron/pull/75
* @A3Brothers made their first contribution in https://github.com/NativePHP/electron/pull/77
* @kpanuragh made their first contribution in https://github.com/NativePHP/electron/pull/52
* @meliani made their first contribution in https://github.com/NativePHP/electron/pull/89

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.4.0...0.5.0

## 0.4.0 - 2023-08-09

### What's Changed

- Added Laravel Prompts
- Added the option --installer to InstallCommand and DevelopCommand by @SergioGMR in https://github.com/NativePHP/electron/pull/26
- Linux extra condition for electron builder. by @lotharthesavior in https://github.com/NativePHP/electron/pull/57
- fix(linux build commands): add missing 'x64' flag by @pixrr in https://github.com/NativePHP/electron/pull/63

### New Contributors

- @SergioGMR made their first contribution in https://github.com/NativePHP/electron/pull/26
- @lotharthesavior made their first contribution in https://github.com/NativePHP/electron/pull/57
- @pixrr made their first contribution in https://github.com/NativePHP/electron/pull/63

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.3.3...0.4.0

## 0.3.3 - 2023-07-31

### What's Changed

- Fix native:build npm timeout by @drhoussem in https://github.com/NativePHP/electron/pull/54
- Resolve Windows opening config file in default application by @ShaneShipston in https://github.com/NativePHP/electron/pull/60
- Correct NATIVEPHP_PHP_BINARY_PATH by @lonnylot in https://github.com/NativePHP/electron/pull/64
- Fix php path in queue command by @phuclh in https://github.com/NativePHP/electron/pull/58
- Fix queue command environment variables

### New Contributors

- @drhoussem made their first contribution in https://github.com/NativePHP/electron/pull/54
- @ShaneShipston made their first contribution in https://github.com/NativePHP/electron/pull/60
- @lonnylot made their first contribution in https://github.com/NativePHP/electron/pull/64
- @phuclh made their first contribution in https://github.com/NativePHP/electron/pull/58

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.3.2...0.3.3

## 0.3.2 - 2023-07-28

- Bump JS package dependency

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.3.1...0.3.2

## 0.3.1 - 2023-07-27

Fix PHP path resolution

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.3.0...0.3.1

## 0.3.0 - 2023-07-27

### What's Changed

- Added the ability to build Windows and Linux apps 🎉
- Added binary lookup for linux based platform by @kalizi in https://github.com/NativePHP/electron/pull/44

### New Contributors

- @kalizi made their first contribution in https://github.com/NativePHP/electron/pull/44

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.2.0...0.3.0

## 0.2.0 - 2023-07-26

### What's Changed

- Add Windows support by @chrisreedio in https://github.com/NativePHP/electron/pull/33

### New Contributors

- @chrisreedio made their first contribution in https://github.com/NativePHP/electron/pull/33

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.1.2...0.2.0

## 0.1.2 - 2023-07-25

### What's Changed

- Remove yarn dependency when using serve command by @semiherdogan in https://github.com/NativePHP/electron/pull/39
- Add missing php.js file
- Make sure that NPM runs forever when running `artisan:publish`

### New Contributors

- @semiherdogan made their first contribution in https://github.com/NativePHP/electron/pull/39

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.1.1...0.1.2

## 0.1.1 - 2023-07-22

### What's Changed

- fix: README badges by @bensherred in https://github.com/NativePHP/electron/pull/7
- composer package local path remove for production release #14 by @AbmSourav in https://github.com/NativePHP/electron/pull/15
- Package Name Update: Migrating from 'nativephp-electron' to '@nativephp/electron-plugin' by @hogus2037 in https://github.com/NativePHP/electron/pull/16
- Fix install command by @bangbangda in https://github.com/NativePHP/electron/pull/17
- Fix the issue of starting the development server by @mahmoudmohamedramadan in https://github.com/NativePHP/electron/pull/25

### New Contributors

- @bensherred made their first contribution in https://github.com/NativePHP/electron/pull/7
- @AbmSourav made their first contribution in https://github.com/NativePHP/electron/pull/15
- @hogus2037 made their first contribution in https://github.com/NativePHP/electron/pull/16
- @bangbangda made their first contribution in https://github.com/NativePHP/electron/pull/17
- @mahmoudmohamedramadan made their first contribution in https://github.com/NativePHP/electron/pull/25

**Full Changelog**: https://github.com/NativePHP/electron/compare/0.1.0...0.1.1

## 0.1.0 - 2023-07-20

### 🎉 NativePHP is here!
