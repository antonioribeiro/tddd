<?php

namespace PragmaRX\TestsWatcher\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use PragmaRX\TestsWatcher\Vendor\Laravel\ServiceProvider as TestsWatcherServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected $database;

    protected function config($key, $value = null)
    {
        if (!is_null($value)) {
            app()->config->set("ci.{$key}", $value);
        }

        app()->config->get("ci.{$key}");
    }

    private function configureDatabase()
    {
        if (!file_exists($path = __DIR__.'/databases')) {
            mkdir($path);
        }

        touch($this->database = tempnam($path, 'database.sqlite.'));

        app()->config->set(
            'database.connections.testbench',
            [
                'driver'   => 'sqlite',
                'database' => $this->database,
                'prefix'   => '',
            ]
        );
    }

    private function deleteDatabase()
    {
        @unlink($this->database);
    }

    protected function setUp()
    {
        parent::setUp();

        if (config('ci.enabled')) {
            $this->ci = app('ci');

            app('ci.cache')->flush();
        }

        $this->configureDatabase();

        $this->artisan('migrate:refresh', ['--database' => 'testbench']);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->deleteDatabase();
    }

    protected function getPackageProviders($app)
    {
        $app['config']->set('ci.enabled', true);

        $app['config']->set('ci.geoip_database_path', __DIR__.'/geoipdb');

        $app['config']->set('ci.enable_country_search', true);

        $app['config']->set('ci.cache_expire_time', 10);

        return [
            TestsWatcherServiceProvider::class,
        ];
    }
}
