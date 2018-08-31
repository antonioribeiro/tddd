<?php

namespace PragmaRX\Tddd\Package;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use PragmaRX\Tddd\Package\Services\Cache;
use PragmaRX\Tddd\Package\Services\Config;
use PragmaRX\Tddd\Package\Listeners\Notify;
use PragmaRX\Tddd\Package\Support\Notifier;
use PragmaRX\Tddd\Package\Events\TestsFailed;
use PragmaRX\Tddd\Package\Listeners\MarkAsNotified;
use PragmaRX\Tddd\Package\Events\UserNotifiedOfFailure;
use PragmaRX\Tddd\Package\Console\Commands\TestCommand;
use PragmaRX\Tddd\Package\Console\Commands\WatchCommand;
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
    protected function loadConfig()
    {
        $this->config->loadConfig();
    }

    /**
     * Configure migrations path.
     */
    protected function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Configure views path.
     */
    protected function loadViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pragmarx/tddd');
    }

    /**
     * Configure config path.
     */
    protected function publishConfiguration()
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

        $this->registerCache();

        $this->registerWatchCommand();

        $this->registerTestCommand();

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
     * Register event listeners.
     */
    protected function registerEventListeners()
    {
        Event::listen(TestsFailed::class, Notify::class);

        Event::listen(UserNotifiedOfFailure::class, MarkAsNotified::class);
    }

    /**
     * Register the watch command.
     */
    protected function registerNotifier()
    {
        $this->app->singleton('tddd.notifier', function () {
            return new Notifier();
        });
    }

    /**
     * Register the watch command.
     */
    protected function registerWatchCommand()
    {
        $this->app->singleton('tddd.watch.command', function () {
            return new WatchCommand();
        });

        $this->commands('tddd.watch.command');
    }

    /**
     * Register the test command.
     */
    protected function registerTestCommand()
    {
        $this->app->singleton('tddd.test.command', function () {
            return new TestCommand();
        });

        $this->commands('tddd.test.command');
    }

    /**
     * Register service service.
     */
    protected function registerService()
    {
        $this->app->singleton('tddd', function () {
            return app('PragmaRX\Tddd\Package\Service');
        });
    }

    /**
     * Register service watcher.
     */
    protected function registerWatcher()
    {
        $this->app->singleton('tddd.watcher', function () {
            return app('PragmaRX\Tddd\Package\Services\Watcher');
        });
    }

    /**
     * Register service tester.
     */
    protected function registerTester()
    {
        $this->app->singleton('tddd.tester', function () {
            return app('PragmaRX\Tddd\Package\Services\Tester');
        });
    }

    /**
     * Register service tester.
     */
    protected function registerCache()
    {
        $this->app->singleton('tddd.cache', function () {
            return new Cache();
        });
    }

    /**
     * Register service tester.
     */
    protected function registerConfig()
    {
        $config = $this->config = app('PragmaRX\Tddd\Package\Services\Config');

        $this->app->singleton('tddd.config', function () use ($config) {
            return $config;
        });
    }

    /**
     * Register the resource watcher.
     */
    protected function registerResourceWatcher()
    {
        $this->app->register('JasonLewis\ResourceWatcher\Integration\LaravelServiceProvider');
    }

    /**
     * Register all routes.
     */
    protected function loadRoutes()
    {
        Route::group([
            'prefix'     => config('tddd.routes.prefixes.global'),
            'namespace'  => 'PragmaRX\Tddd\Package\Http\Controllers',
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
