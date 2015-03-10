<?php

namespace PragmaRX\Ci\Vendor\Laravel;
 
use PragmaRX\Ci\Vendor\Laravel\Console\Commands\TestCommand;
use PragmaRX\Ci\Vendor\Laravel\Console\Commands\WatchCommand;
use PragmaRX\Support\ServiceProvider as PragmaRXServiceProvider;

class ServiceProvider extends PragmaRXServiceProvider {

    const PACKAGE_NAMESPACE = 'pragmarx/ci';

	protected $packageVendor = 'pragmarx';

	protected $packageVendorCapitalized = 'PragmaRX';

	protected $packageName = 'ci';

	protected $packageNameCapitalized = 'Ci';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

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
		$me = $this;

		$this->app->singleton('ci.watcher', function($app) use ($me)
		{
			$watcher = $this->app->make('PragmaRX\Ci\Services\Watcher');

			$watcher->setConfig($me->getConfig());

			return $watcher;
		});
	}

	private function registerTester()
	{
		$me = $this;

		$this->app->singleton('ci.tester', function($app) use ($me)
		{
			$tester = $this->app->make('PragmaRX\Ci\Services\Tester');

			$tester->setConfig($me->getConfig());

			return $tester;
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
