<?php namespace JasonLewis\ResourceWatcher\Integration;

use Illuminate\Support\ServiceProvider;
use JasonLewis\ResourceWatcher\Tracker;
use JasonLewis\ResourceWatcher\Watcher;

class LaravelServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['watcher'] = $this->app->share(function($app)
		{
			$tracker = new Tracker;

			return new Watcher($tracker, $app['files']);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('watcher');
	}

}
