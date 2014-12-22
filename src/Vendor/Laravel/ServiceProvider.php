<?php

namespace PragmaRX\Ci\Vendor\Laravel;
 
use PragmaRX\Ci\Vendor\Laravel\Console\Commands\TestCommand;
use PragmaRX\Ci\Vendor\Laravel\Console\Commands\WatchCommand;
use PragmaRX\Support\ServiceProvider as PragmaRXServiceProvider;

use Illuminate\Foundation\AliasLoader as IlluminateAliasLoader;

class ServiceProvider extends PragmaRXServiceProvider {

    const PACKAGE_NAMESPACE = 'pragmarx/ci';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package(self::PACKAGE_NAMESPACE, self::PACKAGE_NAMESPACE, __DIR__.'/../..');

        if( $this->app['config']->get(self::PACKAGE_NAMESPACE.'::create_ci_alias') )
        {
            IlluminateAliasLoader::getInstance()->alias(
                                                            $this->getConfig('ci_alias'),
                                                            'PragmaRX\Ci\Vendor\Laravel\Facade'
                                                        );
        }

        $this->wakeUp();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
	    parent::register();

	    $this->registerResourceWatcher();

	    $this->registerWatcher();

	    $this->registerTester();

	    $this->registerWatchCommand();

	    $this->registerTestCommand();

	    $this->registerRoutes();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('ci');
    }

	private function registerWatchCommand()
	{
        $this->app['ci.watch.command'] = $this->app->share(function($app)
        {
            return new WatchCommand();
        });

		$this->commands('ci.watch.command');
	}

	private function registerTestCommand()
	{
        $this->app['ci.test.command'] = $this->app->share(function($app)
        {
            return new TestCommand();
        });

		$this->commands('ci.test.command');
	}

	private function registerWatcher()
	{
		$this->app->singleton('ci.watcher', function($app)
		{
			return $this->app->make('PragmaRX\Ci\Services\Watcher');
		});
	}

	private function registerTester()
	{
		$this->app->singleton('ci.tester', function($app)
		{
			return $this->app->make('PragmaRX\Ci\Services\Tester');
		});
	}

	private function registerResourceWatcher()
	{
		$this->app->register('JasonLewis\ResourceWatcher\Integration\LaravelServiceProvider');
	}

	private function registerRoutes()
	{
		$router = $this->app->make('router');

		$router->group(['namespace' => 'PragmaRX\Ci\Vendor\Laravel\Http\Controllers'], function() use ($router)
		{
			$router->get('tests/run/all/{project_id}', 'DashboardController@runAll');

			$router->get('tests/run/{test_id?}', 'DashboardController@runTest');

			$router->get('tests/enable/{enable}/{project_id}/{test_id?}', 'DashboardController@enableTests');

			$router->get('tests/{project_id?}', 'DashboardController@allTests');

			$router->get('projects', 'DashboardController@allProjects');
		});
	}

}
