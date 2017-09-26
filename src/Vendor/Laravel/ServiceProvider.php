<?php

namespace PragmaRX\Ci\Vendor\Laravel;

use Illuminate\Support\Facades\Route;
use PragmaRX\Ci\Vendor\Laravel\Console\Commands\TestCommand;
use PragmaRX\Ci\Vendor\Laravel\Console\Commands\WatchCommand;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot Service Provider.
     *
     */
    public function boot()
    {
        $this->publishConfiguration();

        $this->loadMigrations();

        $this->loadRoutes();

        $this->loadViews();
    }

    /**
     * Configure migrations path.
     *
     */
    private function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../migrations');
    }

    /**
     * Configure views path.
     *
     */
    private function loadViews()
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'pragmarx/ci');
    }

    /**
     * Configure config path.
     *
     */
    private function publishConfiguration()
    {
        $this->publishes([
            __DIR__.'/../../config/ci.php' => config_path('ci.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (! defined('CI_PATH')) {
            define('CI_PATH', realpath(__DIR__.'/../../../'));
        }

        $this->registerResourceWatcher();

	    $this->registerWatcher();

	    $this->registerTester();

	    $this->registerWatchCommand();

	    $this->registerTestCommand();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['ci'];
    }

    /**
     * Register the watch command.
     *
     */
    private function registerWatchCommand()
	{
        $this->app->singleton('ci.watch.command', function($app)
        {
            return new WatchCommand();
        });

		$this->commands('ci.watch.command');
	}

    /**
     * Register the test command.
     *
     */
    private function registerTestCommand()
	{
        $this->app->singleton('ci.test.command', function()
        {
            return new TestCommand();
        });

		$this->commands('ci.test.command');
	}

    /**
     * Register service watcher.
     *
     */
    private function registerWatcher()
	{
		$this->app->singleton('ci.watcher', function($app)
		{
			$watcher = $this->app->make('PragmaRX\Ci\Services\Watcher');

			$watcher->setConfig(config('ci'));

			return $watcher;
		});
	}

    /**
     * Register service tester.
     *
     */
    private function registerTester()
	{
		$this->app->singleton('ci.tester', function($app)
		{
			$tester = $this->app->make('PragmaRX\Ci\Services\Tester');

			$tester->setConfig(config('ci'));

			return $tester;
		});
	}

    /**
     * Register the resource watcher.
     *
     */
    private function registerResourceWatcher()
	{
		$this->app->register('JasonLewis\ResourceWatcher\Integration\LaravelServiceProvider');
	}

    /**
     * Register all routes.
     *
     */
    private function loadRoutes()
	{
        Route::group([
            'prefix' => '/ci-watcher',
            'namespace' => 'PragmaRX\Ci\Vendor\Laravel\Http\Controllers',
            'middleware' => 'web',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        });
	}

	/**
	 * Get the root directory for this ServiceProvider
	 *
	 * @return string
	 */
	public function getRootDirectory()
	{
		return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..';
	}
}
