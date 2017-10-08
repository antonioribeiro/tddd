<?php

$basePath = base_path();

return [

    /**
     * Names & titles
     *
     */
    'names' => [
        'dashboard' => $name = 'Laravel Tests-Watcher',

        'watcher' => $name.' - Watcher',

        'worker' => $name.' - Worker',
    ],

    /**
     * Route URI prefix
     *
     */
    'url_prefix' => '/tests-watcher',

    /**
     * Projects
     *
     */
    'projects' => [
        'app' => [
            'path' => $basePath,
            'watch_folders' => [
                'app',
                'tests'
            ],
            'exclude' => [],
            'depends' => [],
            'tests_path' => 'tests',
            'suites' => [
                'feature' => [
                    'tester' => 'phpunit',
                    'tests_path' => 'Feature',
                    'command_options' => '',
                    'file_mask' => '*Test.php',
                    'retries' => 0,
                ],

                'unit' => [
                    'tester' => 'phpunit',
                    'tests_path' => 'Unit',
                    'command_options' => '',
                    'file_mask' => '*Test.php',
                    'retries' => 0,
                ],
            ],
        ],
    ],

    /**
     * Notifications
     *
     */
    'notifications' => [
        'enabled' => true,

        'notify_on' => [
            'panel' => false,
            'check' => true,
            'string' => true,
            'resource' => false,
        ],

        'routes' => [
            'dashboard' => 'tests-watcher.dashboard'
        ],

        'action-title' => 'Tests Failed',

        'action_message' => "One or more tests have failed.",

        'from' => [
            'name' => $name,

            'address' => 'laravel-tw@mydomain.com',

            'icon_emoji' => '',

            'icon_url' => 'https://emojipedia-us.s3.amazonaws.com/thumbs/120/apple/96/lady-beetle_1f41e.png'
        ],

        'users' => [
            'model' => PragmaRX\TestsWatcher\Vendor\Laravel\Entities\User::class, // App\User::class,

            'emails' => [
                'laravel-ci@mydomain.com'
            ],
        ],

        'channels' => [
            'mail' => [
                'enabled' => false,
                'sender' => PragmaRX\TestsWatcher\Notifications\Channels\Mail::class,
            ],

            'slack' => [
                'enabled' => true,
                'sender' => PragmaRX\TestsWatcher\Notifications\Channels\Slack::class,
            ],
        ],

        'notifier' => 'PragmaRX\TestsWatcher\Notifications',
    ],

    /**
     * Editor
     *
     */
    'editor' => [
        'type' => 'PHPStorm',

        'bin' => '/usr/local/bin/pstorm'
    ],

    /**
     * tee
     *
     */
    'tee' => '/usr/bin/tee',

    /**
     * Temp path
     *
     */
    'tmp' => sys_get_temp_dir(),

    /**
     * Testers
     *
     */
    'testers' => [

        'phpunit' => [
            'command' => 'vendor/bin/phpunit',
        ],

        'dusk' => [
            'command' => 'php artisan dusk',
            'output_folder' => "{$basePath}/tests/Browser/screenshots",
            'output_html_fail_extension' => '.fail.html',
            'output_png_fail_extension' => '.fail.png',
            'require_tee' => true,
            'error_pattern' => '(Failures|Errors): [0-9]+', // regex, only for tee results
        ],

        'codeception' => [
            'command' => 'sh %project_path%/vendor/bin/codecept run',
            'output_folder' => 'tests/_output',
            'output_html_fail_extension' => '.fail.html',
            'output_png_fail_extension' => '.fail.png',
        ],

        'phpspec' => [
            'command' => 'phpspec run',
        ],

        'behat' => [
            'command' => 'sh vendor/bin/behat',
        ],

        'atoum' => [
            'command' => 'sh vendor/bin/atoum',
        ],

        'tester' => [
            'command' => 'sh vendor/bin/tester',
        ],

    ],

    /**
     * Progress
     *
     */
    'show_progress' => false,

];
