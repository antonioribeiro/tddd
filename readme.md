# Continuous Integration Package

[![Latest Stable Version](https://img.shields.io/packagist/v/pragmarx/ci.svg?style=flat-square)](https://packagist.org/packages/pragmarx/ci) [![License](https://img.shields.io/badge/license-BSD_3_Clause-brightgreen.svg?style=flat-square)](LICENSE)

### A Self-Hosted Continuous Integration Service and a Dashboard.

#### Compatible with Laravel 4 & 5

## Dashboard View

The Dashboard, built using [Facebook's React Javascript Library](http://facebook.github.io/react/), displays and gives you control of your projects and project tests. This is what you'll see and will be able to do with it:

* Project List: click a project link to see all its tests.
* Checkboxes to enable/disable a test. Once disabled if the watcher catches a change in resources, that test will not fire.
* "Run" button, to manually fire a test.
* Test 'last run' time.
* Current Test "State": tests states are "running", "queued", "ok" and "failed".
* "Show" button, to display the error log of failed tests.

### Screenshots

#### Dashboard
![visits](https://raw.githubusercontent.com/antonioribeiro/ci/master/src/views/screenshots/dashboard.png)

#### Error Log
![visits](https://raw.githubusercontent.com/antonioribeiro/ci/master/src/views/screenshots/errorlog1.png)

![visits](https://raw.githubusercontent.com/antonioribeiro/ci/master/src/views/screenshots/errorlog2.png)

![visits](https://raw.githubusercontent.com/antonioribeiro/ci/master/src/views/screenshots/errorlog3.png)

## Command Line Interface

The Artisan commands **Watcher** and **Tester** are responsible for watching resources and firing tests, respectively:

### Watcher

Keep track of your files and enqueue your tests every time a project or test file is changed. If a project file changes, it will enqueue all your tests, if a test file changes, it will enqueue only that particular test. This is how you run it:

    php artisan ci:watch

### Tester

Responsible for taking tests from the run queue, execute it and log the results. Tester will only execute enabled tests. This is how you run it:

    php artisan ci:test

## Test Framework Compatibility

This package was tested and is known to be compatible with

* [Codeception](http://codeception.com/)
* [PHPUnit](https://phpunit.de/)
* [phpspec](http://www.phpspec.net/)
* [behat](http://docs.behat.org/)
* [atoum](https://github.com/atoum/atoum)
* [Nette Tester](http://tester.nette.org/en/)

## Installing

Require it with [Composer](http://getcomposer.org/):

    $ composer require pragmarx/ci

Add the service provider to your app/config/app.php:

    'PragmaRX\Ci\Vendor\Laravel\ServiceProvider',

Create a database, configure on your Laravel app and migrate it

    php artisan migrate --package=pragmarx/ci

Publish Ci configuration:

On Laravel 4.*

    php artisan config:publish pragmarx/ci

On Laravel 5.*

    php artisan publish:config pragmarx/ci

Edit the file `app/config/packages/pragmarx/ci/config.php` add your testers:

	'testers' => [
		'codeception' => [
			'command' => 'sh %project_path%/vendor/bin/codecept run',
		],

		'phpunit' => [
			'command' => 'phpunit',
		],
	],

Also your projects and test suites:

	'projects' => [
		'myproject' => [
			'path' => '/var/www/myproject.dev',
			'watch_folders' => ['app', 'tests'],
			'exclude_folders' => ['tests/_output'],
			'tests_path' => 'tests',
			'suites' => [
				'functional' => [
					'tester' => 'codeception',
					'tests_path' => 'functional',
					'command_options' => 'functional',
					'file_mask' => '*Cept.php',
					'retries' => 3,
				]
			],
		],

	],

For the Dashboard you just need to create a route and add render this view:

    return View::make('pragmarx/ci::dashboard');

## Requirements

- Laravel 4.1+ or 5
- PHP 5.3.7+

## Author

[Antonio Carlos Ribeiro](http://twitter.com/iantonioribeiro)

## License

Laravel Ci is licensed under the BSD 3-Clause License - see the `LICENSE` file for details

## Contributing

Pull requests and issues are welcome.
