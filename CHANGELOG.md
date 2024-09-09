# Changelog

All notable changes to `nativephp-laravel` will be documented in this file.

## 0.5.6 - 2024-09-09

### What's Changed

* Fix chainable by @simonhamp in https://github.com/NativePHP/laravel/pull/360
* Fixed Tests by @RobertWesner in https://github.com/NativePHP/laravel/pull/362

**Full Changelog**: https://github.com/NativePHP/laravel/compare/0.5.5...0.5.6

## 0.5.5 - 2024-09-02

### What's Changed

* Battery/AC power, plus more by @danjohnson95 in https://github.com/NativePHP/laravel/pull/355
* Safe storage by @simonhamp in https://github.com/NativePHP/laravel/pull/357

**Full Changelog**: https://github.com/NativePHP/laravel/compare/0.5.4...0.5.5

## 0.5.4 - 2024-08-24

### What's Changed

* Add fullscreenable support by @simonhamp in https://github.com/NativePHP/laravel/pull/340
* Implemented minimizing by @RobertWesner in https://github.com/NativePHP/laravel/pull/347
* Fluent API for opening windows maximized/minimized by @simonhamp in https://github.com/NativePHP/laravel/pull/349

### New Contributors

* @RobertWesner made their first contribution in https://github.com/NativePHP/laravel/pull/347

**Full Changelog**: https://github.com/NativePHP/laravel/compare/0.5.3...0.5.4

## 0.5.3 - 2024-07-18

### What's Changed

* Add the reload() method to the WindowManager by @shomisha in https://github.com/NativePHP/laravel/pull/294
* Add `trafficLightPosition()` method on Window by @sarukomine in https://github.com/NativePHP/laravel/pull/310
* Add a new `System::timezone` helper to detect and normalise system time zone data
* Fix `native:migrate:fresh` so that it behaves more like Laravel's `migrate:fresh` (e.g. you can use seeders etc)
* Bump dependabot/fetch-metadata from 2.1.0 to 2.2.0 by @dependabot in https://github.com/NativePHP/laravel/pull/333

### New Contributors

* @shomisha made their first contribution in https://github.com/NativePHP/laravel/pull/294
* @sarukomine made their first contribution in https://github.com/NativePHP/laravel/pull/310

**Full Changelog**: https://github.com/NativePHP/laravel/compare/0.5.2...0.5.3

## 0.5.2 - 2024-05-02

### What's Changed

* Fixes a regression introduced in 0.5.1

**Full Changelog**: https://github.com/NativePHP/laravel/compare/0.5.1...0.5.2

## 0.5.1 - 2024-05-02

### What's Changed

* Add a default `NATIVEPHP_APP_ID` to fix #284
* Fix for #175 `native:migrate:fresh` command @me-shaon in https://github.com/NativePHP/laravel/pull/198
* Add hide window functionality by @braceyourself in https://github.com/NativePHP/laravel/pull/144
* Update Tests workflow by @milwad-dev in https://github.com/NativePHP/laravel/pull/265
* Bump ramsey/composer-install from 2 to 3 by @dependabot in https://github.com/NativePHP/laravel/pull/255
* Bump dependabot/fetch-metadata from 1.6.0 to 2.0.0 by @dependabot in https://github.com/NativePHP/laravel/pull/260
* Bump aglipanci/laravel-pint-action from 2.3.1 to 2.4 by @dependabot in https://github.com/NativePHP/laravel/pull/268
* Bump dependabot/fetch-metadata from 2.0.0 to 2.1.0 by @dependabot in https://github.com/NativePHP/laravel/pull/275

### New Contributors

* @braceyourself made their first contribution in https://github.com/NativePHP/laravel/pull/144

**Full Changelog**: https://github.com/NativePHP/laravel/compare/0.5.0...0.6.0

## 0.5.0 - 2024-04-01

### What's Changed

* Add Laravel 11 support by @meliani in https://github.com/NativePHP/laravel/pull/262
* Add printToPDF function by @basst85 in https://github.com/NativePHP/laravel/pull/104
* Add method to update an existing context-menu of the menu bar by @bbredewold in https://github.com/NativePHP/laravel/pull/108
* Add required config for using GitHub as an updater provider by @danjohnson95 in https://github.com/NativePHP/laravel/pull/189
* Add ability to exclude files and entire folders from built application by @nexxai in https://github.com/NativePHP/laravel/pull/165
* Add ability to use string events by @LukeTowers in https://github.com/NativePHP/laravel/pull/67
* Fix 'native:db:seed' command not working by @me-shaon in https://github.com/NativePHP/laravel/pull/199
* Fix an issue that prevented setting the position on open windows by @curtisblackwell in https://github.com/NativePHP/laravel/pull/215
* Fix PHP Fatal Error deleteDirectoryRecursive function in MinifyApplicationCommand by @LunashaGit in https://github.com/NativePHP/laravel/pull/249
* Bump actions/checkout from 3 to 4 by @dependabot in https://github.com/NativePHP/laravel/pull/208
* Bump stefanzweifel/git-auto-commit-action from 4 to 5 by @dependabot in https://github.com/NativePHP/laravel/pull/217
* Bump aglipanci/laravel-pint-action from 2.3.0 to 2.3.1 by @dependabot in https://github.com/NativePHP/laravel/pull/240

### New Contributors

* @me-shaon made their first contribution in https://github.com/NativePHP/laravel/pull/199
* @bbredewold made their first contribution in https://github.com/NativePHP/laravel/pull/108
* @danjohnson95 made their first contribution in https://github.com/NativePHP/laravel/pull/189
* @curtisblackwell made their first contribution in https://github.com/NativePHP/laravel/pull/215
* @nexxai made their first contribution in https://github.com/NativePHP/laravel/pull/165
* @LunashaGit made their first contribution in https://github.com/NativePHP/laravel/pull/249
* @LukeTowers made their first contribution in https://github.com/NativePHP/laravel/pull/67
* @meliani made their first contribution in https://github.com/NativePHP/laravel/pull/262

**Full Changelog**: https://github.com/NativePHP/laravel/compare/0.4.0...0.5.0

## 0.4.0 - 2023-08-09

### What's Changed

- Added the ability to remove custom .env keys when bundling the application
- Allow custom php.ini settings by @mpociot in https://github.com/NativePHP/laravel/pull/98
- Add method to configure disks by @mpociot in https://github.com/NativePHP/laravel/pull/99
- Printer support by @mpociot in https://github.com/NativePHP/laravel/pull/103
- Adds 'php artisan native:migrate fresh' command by @shanerbaner82 in https://github.com/NativePHP/laravel/pull/81
- Add Tests For `Windows` by @milwad-dev in https://github.com/NativePHP/laravel/pull/100
- import VerifyCsrfToken and refactor by @JaberWiki in https://github.com/NativePHP/laravel/pull/110
- Modified return type of clipboard image method by @blankRSD in https://github.com/NativePHP/laravel/pull/111
- add windowPosition by @DanielHudson in https://github.com/NativePHP/laravel/pull/112
- Implement MenuBarDroppedFiles event by @ArondeParon in https://github.com/NativePHP/laravel/pull/113

### New Contributors

- @mpociot made their first contribution in https://github.com/NativePHP/laravel/pull/98
- @shanerbaner82 made their first contribution in https://github.com/NativePHP/laravel/pull/81
- @JaberWiki made their first contribution in https://github.com/NativePHP/laravel/pull/110
- @blankRSD made their first contribution in https://github.com/NativePHP/laravel/pull/111
- @DanielHudson made their first contribution in https://github.com/NativePHP/laravel/pull/112
- @ArondeParon made their first contribution in https://github.com/NativePHP/laravel/pull/113

**Full Changelog**: https://github.com/NativePHP/laravel/compare/0.3.0...0.4.0

## 0.3.0 - 2023-07-31

### What's Changed

- Add native:db:seed command by @phuclh in https://github.com/NativePHP/laravel/pull/82
- Option to show/hide menu on Windows / Linux by @ShaneShipston in https://github.com/NativePHP/laravel/pull/84
- Add tests for `MenuBar` by @milwad-dev in https://github.com/NativePHP/laravel/pull/31

### New Contributors

- @phuclh made their first contribution in https://github.com/NativePHP/laravel/pull/82
- @ShaneShipston made their first contribution in https://github.com/NativePHP/laravel/pull/84

**Full Changelog**: https://github.com/NativePHP/laravel/compare/0.2.0...0.3.0

## 0.2.0 - 2023-07-28

### What's changed

- Added the new `Shell` Facade that allows you to open files in the explorer/finder, open urls in the default app, trash files, etc.

**Full Changelog**: https://github.com/NativePHP/laravel/compare/0.1.1...0.2.0

## 0.1.1 - 2023-07-25

### What's Changed

- Add the ability to create context-menu-only menubar apps
- Storage path and database path now only get overwritten when the app is running in production mode, making debugging easier
- fix: README badges by @bensherred in https://github.com/NativePHP/laravel/pull/11
- Add test for `CreateSecurityCookieController` by @milwad-dev in https://github.com/NativePHP/laravel/pull/12
- Use `abort_if` by @milwad-dev in https://github.com/NativePHP/laravel/pull/13
- Update ProgressBar.php by @ddobren in https://github.com/NativePHP/laravel/pull/33
- Add facade methods by @milwad-dev in https://github.com/NativePHP/laravel/pull/30
- Create CONTRIBUTING.md by @ddobren in https://github.com/NativePHP/laravel/pull/32
- Create Native\Laravel\Facades\Notification by @Mombuyish in https://github.com/NativePHP/laravel/pull/25
- Add node version to bug issue template by @olssonm in https://github.com/NativePHP/laravel/pull/54
- update README.md add section documentation by @artmxra7 in https://github.com/NativePHP/laravel/pull/51
- Update NativeAppServiceProvider.php.stub to use correct namespace by @semiherdogan in https://github.com/NativePHP/laravel/pull/55
- update README.md by @artmxra7 in https://github.com/NativePHP/laravel/pull/60
- Support for fullscreen, kiosk-mode and maximize by @basst85 in https://github.com/NativePHP/laravel/pull/68

### New Contributors

- @bensherred made their first contribution in https://github.com/NativePHP/laravel/pull/11
- @milwad-dev made their first contribution in https://github.com/NativePHP/laravel/pull/12
- @ddobren made their first contribution in https://github.com/NativePHP/laravel/pull/33
- @Mombuyish made their first contribution in https://github.com/NativePHP/laravel/pull/25
- @olssonm made their first contribution in https://github.com/NativePHP/laravel/pull/54
- @artmxra7 made their first contribution in https://github.com/NativePHP/laravel/pull/51
- @semiherdogan made their first contribution in https://github.com/NativePHP/laravel/pull/55
- @basst85 made their first contribution in https://github.com/NativePHP/laravel/pull/68

**Full Changelog**: https://github.com/NativePHP/laravel/compare/0.1.0...0.1.1

## 0.1.0 - 2023-07-20

### 🎉 NativePHP is here!
