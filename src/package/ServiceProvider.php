<?php

namespace PragmaRX\TestsWatcher\Package;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use PragmaRX\TestsWatcher\Package\Console\Commands\ClearCommand;
use PragmaRX\TestsWatcher\Package\Console\Commands\TestCommand;
use PragmaRX\TestsWatcher\Package\Console\Commands\WatchCommand;
use PragmaRX\TestsWatcher\Package\Events\TestsFailed;
use PragmaRX\TestsWatcher\Package\Events\UserNotifiedOfFailure;
use PragmaRX\TestsWatcher\Package\Listeners\MarkAsNotified;
use PragmaRX\TestsWatcher\Package\Listeners\Notify;
use PragmaRX\TestsWatcher\Package\Services\Config;
use PragmaRX\TestsWatcher\Package\Support\Notifier;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Config instance.
     *
     * @var Config
     */
    protected $config;

    /**
     * Boot Service Provider.
     */
    public function boot()
    {
        $this->publishConfiguration();

        $this->loadConfig();

        $this->loadMigrations();

        $this->loadRoutes();

        $this->loadViews();
    }

    /**
     * Load config files to Laravel config.
     */
    private function loadConfig()
    {
        $this->config->loadConfig();
    }

    /**
     * Configure migrations path.
     */
    private function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Configure views path.
     */
    private function loadViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pragmarx/tddd');
    }

    /**
     * Configure config path.
     */
    private function publishConfiguration()
    {
        $this->publishes([
            __DIR__.'/../config' => config_path(),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (!defined('TDDD_PATH')) {
            define('TDDD_PATH', realpath(__DIR__.'/../../'));
        }

        $this->registerResourceWatcher();

        $this->registerService();

        $this->registerWatcher();

        $this->registerTester();

        $this->registerConfig();

        $this->registerWatchCommand();

        $this->registerTestCommand();

        $this->registerClearCommand();

        $this->registerNotifier();

        $this->registerEventListeners();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['tddd'];
    }

    /**
     * Register the clear command.
     */
    private function registerClearCommand()
    {
        $this->app->singleton('tddd.clear.command', function () {
            return new ClearCommand();
        });

        $this->commands('tddd.clear.command');
    }

    /**
     * Register event listeners.
     */
    private function registerEventListeners()
    {
        Event::listen(TestsFailed::class, Notify::class);

        Event::listen(UserNotifiedOfFailure::class, MarkAsNotified::class);
    }

    /**
     * Register the watch command.
     */
    private function registerNotifier()
    {
        $this->app->singleton('tddd.notifier', function () {
            return new Notifier();
        });
    }

    /**
     * Register the watch command.
     */
    private function registerWatchCommand()
    {
        $this->app->singleton('tddd.watch.command', function () {
            return new WatchCommand();
        });

        $this->commands('tddd.watch.command');
    }

    /**
     * Register the test command.
     */
    private function registerTestCommand()
    {
        $this->app->singleton('tddd.test.command', function () {
            return new TestCommand();
        });

        $this->commands('tddd.test.command');
    }

    /**
     * Register service service.
     */
    private function registerService()
    {
        $this->app->singleton('tddd', function () {
            return app('PragmaRX\TestsWatcher\Package\Service');
        });
    }

    /**
     * Register service watcher.
     */
    private function registerWatcher()
    {
        $this->app->singleton('tddd.watcher', function () {
            return app('PragmaRX\TestsWatcher\Package\Services\Watcher');
        });
    }

    /**
     * Register service tester.
     */
    private function registerTester()
    {
        $this->app->singleton('tddd.tester', function () {
            return app('PragmaRX\TestsWatcher\Package\Services\Tester');
        });
    }

    /**
     * Register service tester.
     */
    private function registerConfig()
    {
        $config = $this->config = app('PragmaRX\TestsWatcher\Package\Services\Config');

        $this->app->singleton('tddd.config', function () use ($config) {
            return $config;
        });
    }

    /**
     * Register the resource watcher.
     */
    private function registerResourceWatcher()
    {
        $this->app->register('JasonLewis\ResourceWatcher\Integration\LaravelServiceProvider');
    }

    /**
     * Register all routes.
     */
    private function loadRoutes()
    {
        Route::group([
            'prefix'     => config('routes.prefixes.global'),
            'namespace'  => 'PragmaRX\TestsWatcher\Package\Http\Controllers',
            'middleware' => 'web',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * Get the root directory for this ServiceProvider.
     *
     * @return string
     */
    public function getRootDirectory()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..';
    }
}
