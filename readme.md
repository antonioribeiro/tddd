# Laravel CI

[![Latest Stable Version](https://poser.pugx.org/pragmarx/ci/v/stable.png)](https://packagist.org/packages/pragmarx/ci) [![License](https://poser.pugx.org/pragmarx/ci/license.png)](https://packagist.org/packages/pragmarx/ci)

###A Laravel 4 & 5 Self-Hosted Continuous Integration Service and a Dashboard.

##Dashboard View

The Dashboard displays and gives you control of your projects and project tests, this is what you see and can do with it:

* Project List: click a project link to see all its tests.
* Checkboxes to enable/disable a test. Once disabled if the watcher catches a change in resources, that test will not fire.
* "Run" button, to manually fire a test.
* Test 'last run' time.
* Current Test "State": tests states are "running", "queued", "ok" and "failed".
* "Show" button, to display the error log of failed tests.

##Command Line Interface

The Artisan commands **Watcher** and **Tester** are responsible for watching resources and firing tests, respectively:

###Watcher

Keep track of your files and enqueue your tests every time a project or test file is changed. If a project file changes, it will enqueue all your tests, if a test file changes, it will enqueue only that particular test. This is how you run it:

    php artisan ci:watch

###Tester

Responsible for taking tests from the run queue, execute it and log the results. Tester will only execute enabled tests. This is how you run it:

    php artisan ci:test

###Screenshots

####Dashboard
![visits](https://raw.githubusercontent.com/antonioribeiro/ci/master/src/views/screenshots/dashboard.png)

####Error Log
![visits](https://raw.githubusercontent.com/antonioribeiro/ci/master/src/views/screenshots/errorlog.png)

## Requirements

- Laravel 4.1+
- PHP 5.3.7+

## Test Framework Compatibility

This package was tested and is known to be compatible with [Codeception](http://codeception.com/) and [PHPUnit](https://phpunit.de/), but for it to work you just need to provide a command lines, like

PHPUnit example

    'command' => 'phpunit',

Codeception example being run using the 'sh' interpreter, when the file executable flag cannot be set for some reason:

    'command' => 'sh %project_path%/vendor/bin/codecept run',

So you'll probably be able to use it with many others like Behat and phpspec.

## Installing

Add to your composer.json:

    "pragmarx/ci":"~0.1"

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

## Author

[Antonio Carlos Ribeiro](http://twitter.com/iantonioribeiro)

## License

Laravel Ci is licensed under the BSD 3-Clause License - see the `LICENSE` file for details

## Contributing

Pull requests and issues are welcome.
