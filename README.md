# Tests Watcher

[![Latest Stable Version](https://img.shields.io/packagist/v/pragmarx/ci.svg?style=flat-square)](https://packagist.org/packages/pragmarx/ci)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md) 
[![Downloads](https://img.shields.io/packagist/dt/pragmarx/ci.svg?style=flat-square)](https://packagist.org/packages/pragmarx/ci) 
[![Code Quality](https://img.shields.io/scrutinizer/g/antonioribeiro/ci.svg?style=flat-square)](https://scrutinizer-ci.com/g/antonioribeiro/ci/?branch=master) 
[![Coverage](https://img.shields.io/scrutinizer/coverage/g/antonioribeiro/ci.svg?style=flat-square)](https://scrutinizer-ci.com/g/antonioribeiro/ci/?branch=master) 
[![StyleCI](https://styleci.io/repos/27037779/shield)](https://styleci.io/repos/27037779)

### A Self-Hosted Continuous Testing Service and a Dashboard.

## Dashboard View

* Project List: click a project link to see all its tests.
* Open files directly in your source code editor (PHPStorm, Sublime Text...).
* Error log with source code linked, go strait to the error line in your source code.
* Enable/disable a test. Once disabled if the watcher catches a change in resources, that test will not fire.
* Real time test state: "idle", "running", "queued", "ok" and "failed".
* "Show" button, to display the error log of failed tests.
* Ready for PHPUnit, Laravel Dusk, Codeception, phpspec, atoum and more.
* Highly configurable, watch anything & test everything!

### Videos

- [Preview](https://www.youtube.com/watch?v=sO_aDf3xCgE)
- [Installing](https://youtu.be/AgkKCLNiV8w)

### Screenshots

#### Dashboard

![visits](https://raw.githubusercontent.com/antonioribeiro/ci/master/docs/dashboard.png)

#### Error Log
![visits](https://raw.githubusercontent.com/antonioribeiro/ci/master/docs/errorlog1.png)

![visits](https://raw.githubusercontent.com/antonioribeiro/ci/master/docs/errorlog2.png)

![visits](https://raw.githubusercontent.com/antonioribeiro/ci/master/docs/errorlog3.png)

## Command Line Interface

The Artisan commands **Watcher** and **Tester** are responsible for watching resources and firing tests, respectively:

### Watcher

Keep track of your files and enqueue your tests every time a project or test file is changed. If a project file changes, it will enqueue all your tests, if a test file changes, it will enqueue only that particular test. This is how you run it:

``` bash
php artisan ci:watch
```

### Tester

Responsible for taking tests from the run queue, execute it and log the results. Tester will only execute enabled tests. This is how you run it:

``` bash
php artisan ci:test
```

### Notifications

It uses JoliNotif, so if it's not working on macOS, you can try installing terminal-notifier:

``` bash
brew install terminal-notifier
```

## Test Framework Compatibility

This package was tested and is known to be compatible with

* [Codeception](http://codeception.com/)
* [PHPUnit](https://phpunit.de/)
* [phpspec](http://www.phpspec.net/)
* [behat](http://docs.behat.org/)
* [atoum](https://github.com/atoum/atoum)
* [Nette Tester](http://tester.nette.org/en/)

## Installing

#### PostgreSQL is needed, MySQL support is on the way

### TLDR;

- Install via Composer
- Publish the configuration
- Migrate the database
- Run `php artisan ci:watch & php artisan ci:work &`
- Open `http://<domain.dev>/tests-watcher/dashboard`

### Starte App

You can just use [this starter app](https://github.com/antonioribeiro/tests-watcher-starter) to create an independent dashboard for your tests.

### The long version

Require it with [Composer](http://getcomposer.org/):

``` bash
composer require pragmarx/ci
```

Create a database, configure on your Laravel app and migrate it

``` bash
php artisan migrate
```

Publish Ci configuration:

On Laravel 4.*

Add the service provider to your app/config/app.php:

``` php
'PragmaRX\TestsWatcher\ServiceProvider',
```

``` bash
php artisan config:publish pragmarx/ci
```

On Laravel 5.*

``` bash
php artisan vendor:publish --provider="PragmaRX\TestsWatcher\ServiceProvider"
```

## Example of projects

### Laravel Dusk

``` php
'project bar (dusk)' => [
    'path' => $basePath,
    'watch_folders' => [
        'app',
        'tests/Browser'
    ],
    'exclude' => [
        'tests/Browser/console/',
        'tests/Browser/screenshots/',
    ],
    'depends' => [],
    'tests_path' => 'tests',
    'suites' => [
        'browser' => [
            'tester' => 'dusk',
            'tests_path' => 'Browser',
            'command_options' => '',
            'file_mask' => '*Test.php',
            'retries' => 0,
        ],
    ],
],
```

## Troubleshooting

#### Tests are running fine in terminal but failing in the dashboard? 

You have first to remember they are being executed in isolation, and, also, the environment is not exactly the same, so things like a cache and session may affect yoru results. 

## Requirements

- Laravel 4.1+ or 5
- PHP 5.3.7+

## Author

[Antonio Carlos Ribeiro](http://twitter.com/iantonioribeiro)

## License

Laravel Ci is licensed under the BSD 3-Clause License - see the `LICENSE` file for details

## Contributing

Pull requests and issues are welcome.
