<?php

namespace PragmaRX\Tddd\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use PragmaRX\Tddd\Package\ServiceProvider as TdddServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected $database;

    protected function config($key, $value = '---not-set---')
    {
        if ($value !== '---not-set---') {
            $this->app['config']->set("ci.{$key}", $value);
        }

        return $this->app['config']->get("ci.{$key}");
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
        dd('configure');
        parent::setUp();

        $this->configureDatabase();

        $this->artisan('migrate:fresh', ['--database' => 'testbench']);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->deleteDatabase();
    }

    protected function getPackageProviders($app)
    {
        $this->app = $app;

        return [
            TdddServiceProvider::class,
        ];
    }
}
