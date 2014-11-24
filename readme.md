# Laravel CI

[![Latest Stable Version](https://poser.pugx.org/pragmarx/ci/v/stable.png)](https://packagist.org/packages/pragmarx/ci) [![License](https://poser.pugx.org/pragmarx/ci/license.png)](https://packagist.org/packages/pragmarx/ci)

###A self-hosted Continuous Integration Service and a Dashboard, to control and see test results.

## Dashboard View

The Dashboard shows all your projects and project tests, their current state (running, queued, ok, failed), allowing you to enable/disable them and see the result when they fail, you can also manually run a test by pressing a button.

####Dashboard
![visits](https://raw.githubusercontent.com/antonioribeiro/ci/master/src/views/screenshots/dashboard.png)

####Error Log
![visits](https://raw.githubusercontent.com/antonioribeiro/ci/master/src/views/screenshots/errorlog.png)

## Requirements

- Laravel 4.1+
- PHP 5.3.7+

## Installing

Add to your composer.json:

    "pragmarx/ci": "~0.1"

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

Then you'll have access to the Watcher:

    php artisan ci:watch

This command will keep track of your files and fire your tests every time a project or test file is changed.


Adnd the Tester:

    php artisan ci:test

This command is responsible for taking tests from the queue, execute and log their results.

For the Dashboard you just need to create a route and add render this view:

    return View::make('pragmarx/ci::dashboard');

## Author

[Antonio Carlos Ribeiro](http://twitter.com/iantonioribeiro)

## License

Laravel Ci is licensed under the BSD 3-Clause License - see the `LICENSE` file for details

## Contributing

Pull requests and issues are welcome.
