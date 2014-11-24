# Laravel CI

[![Latest Stable Version](https://poser.pugx.org/pragmarx/ci/v/stable.png)](https://packagist.org/packages/pragmarx/ci) [![License](https://poser.pugx.org/pragmarx/ci/license.png)](https://packagist.org/packages/pragmarx/ci)

###A Continuous Integration service you can host yourself and a Dashboard.

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

Publish ci configuration:

    php artisan config:publish pragmarx/ci

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

## Dashboard

The Dashboard shows all your projects and project tests, their current state (running, queued, ok, failed), allowing you to enable/disable them and see the result when they fail, you can also manually run a test by pressing a button.

http://puu.sh/d30Le/1486a57177.png
http://puu.sh/d30Ok/f19752c9c2.png

## Author

[Antonio Carlos Ribeiro](http://twitter.com/iantonioribeiro)

## License

Laravel Ci is licensed under the BSD 3-Clause License - see the `LICENSE` file for details

## Contributing

Pull requests and issues are welcome.
