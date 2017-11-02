<?php

$basePath = base_path();

return [

    /*
     * Names & titles
     *
     */
    'names' => [
        'dashboard' => $name = 'Test Driven Development Dashboard',

        'watcher' => $name.' - Watcher',

        'worker' => $name.' - Worker',
    ],

    /**
     * Route URI prefix
     *
     */
    'url_prefixes' => [
        'global' => '/tests-watcher',

        'dashboard' => '/dashboard',

        'tests' => '/tests',

        'projects' => '/projects',

        'files' => '/files'
    ],

    /*
     * Regex to match file names and li
     *
     */
    'regex_file_matcher' => '/([A-Za-z0-9\/._-]+)(?::| on line )([1-9][0-9]*)/',

    /*
     * Config file
     */

    'config_file' => config_path('ci.php'),

    /*
     * Regex to match file names and li
     *
     */
    'poll_interval' => 300, // ms

    /*
     * Projects
     *
     */
    'projects' => [
        'project foo (PHPUnit)' => [
            'path'          => $basePath,
            'watch_folders' => [
                'app',
                'tests',
            ],
            'exclude'    => [],
            'depends'    => [],
            'tests_path' => 'tests',
            'suites'     => [
                'feature' => [
                    'tester'          => 'phpunit',
                    'tests_path'      => 'Feature',
                    'command_options' => '',
                    'file_mask'       => '*Test.php',
                    'retries'         => 0,
                    'editor'          => 'phpstorm',
                ],

                'unit' => [
                    'tester'          => 'phpunit',
                    'tests_path'      => 'Unit',
                    'command_options' => '',
                    'file_mask'       => '*Test.php',
                    'retries'         => 0,
                ],
            ],
        ],
    ],

    /*
     * Notifications
     *
     */
    'notifications' => [
        'notify_on' => [
            'fail' => true,
            'pass' => false, // not implemented
        ],

        'routes' => [
            'dashboard' => 'tests-watcher.dashboard',
        ],

        'action-title' => 'Tests Failed',

        'action_message' => 'One or more tests have failed.',

        'from' => [
            'name' => $name,

            'address' => 'laravel-tw@mydomain.com',

            'icon_emoji' => '',

            'icon_url' => 'https://emojipedia-us.s3.amazonaws.com/thumbs/120/apple/96/lady-beetle_1f41e.png',
        ],

        'users' => [
            'model' => PragmaRX\TestsWatcher\Package\Data\Models\User::class, // App\User::class,

            'emails' => [
                'laravel-ci@mydomain.com',
            ],
        ],

        'channels' => [
            'mail' => [
                'enabled' => false,
                'sender'  => PragmaRX\TestsWatcher\Package\Notifications\Channels\Mail::class,
            ],

            'slack' => [
                'enabled' => true,
                'sender'  => PragmaRX\TestsWatcher\Package\Notifications\Channels\Slack::class,
            ],
        ],

        'notifier' => 'PragmaRX\TestsWatcher\Notifications',
    ],

    /*
     * Editors
     *
     */
    'editors' => [
        'phpstorm' => [
            'name' => 'PHPStorm',

            'bin' => '/usr/local/bin/pstorm {file}:{line}',

            'default' => true,
        ],

        'sublime' => [
            'name' => 'SublimeText 3',

            'bin' => '/usr/local/bin/subl {file}:{line}',
        ],

        'vscode' => [
            'name' => 'VSCode',

            'bin' => '/Applications/Visual\ Studio\ Code.app/Contents/Resources/app/bin/code --goto {file}:{line}',
        ],
    ],

    /*
     * tee
     *
     */
    'tee' => '/usr/bin/tee',

    /*
     * script
     *
     */
    'script' => '/usr/bin/script -q %s %s', // sprintf()

    /*
     * Temp path
     *
     */
    'tmp' => sys_get_temp_dir(),

    /*
     * Testers
     *
     */
    'testers' => [

        'phpunit' => [
            'command'        => 'vendor/bin/phpunit',
            'require_script' => true,
        ],

        'dusk' => [
            'command'                    => 'php artisan dusk',
            'output_folder'              => "{$basePath}/tests/Browser/screenshots",
            'output_html_fail_extension' => '.fail.html',
            'output_png_fail_extension'  => '.fail.png',
            'require_tee'                => false,
            'require_script'             => true,
            'error_pattern'              => '(Failures|Errors): [0-9]+', // regex, only for tee results
        ],

        'codeception' => [
            'command'                    => 'sh %project_path%/vendor/bin/codecept run',
            'output_folder'              => 'tests/_output',
            'output_html_fail_extension' => '.fail.html',
            'output_png_fail_extension'  => '.fail.png',
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

        'jest' => [
            'command'                    => 'npm test',
            'require_script'             => true,
            'output_folder'              => 'tests/__snapshots__',
            'output_html_fail_extension' => '.snap',
        ],

        'react-scripts' => [
            'env'            => 'CI=true',
            'command'        => 'npm test',
            'require_script' => true,
            'error_pattern'  => 'Test\s+Suites:\s+[0-9]+\s+failed', // regex, only for tee results
        ],

        'rake' => [
            'command'        => 'bin/rails test',
            'require_script' => true,
            'error_pattern'  => 'Test\s+Suites:\s+[0-9]+\s+failed', // regex, only for tee results
        ],

        'ava' => [
            'command'        => 'node_modules/.bin/ava --verbose',
            'require_script' => true,
            'error_pattern'  => '[1-9]+\s+(exception|failure)', // regex, only for tee results
        ],

    ],

    /*
     * Progress
     *
     */
    'show_progress' => false,

];
