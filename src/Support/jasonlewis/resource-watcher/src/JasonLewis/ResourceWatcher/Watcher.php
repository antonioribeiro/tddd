<?php namespace JasonLewis\ResourceWatcher;

use Closure;
use SplFileInfo;
use RuntimeException;
use Illuminate\Filesystem\Filesystem;
use JasonLewis\ResourceWatcher\Resource\FileResource;
use JasonLewis\ResourceWatcher\Resource\DirectoryResource;

class Watcher {

	/**
	 * Tracker instance.
	 *
	 * @var \JasonLewis\ResourceWatcher\Tracker
	 */
	protected $tracker;

	/**
	 * Illuminate filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * Indicates if the watcher is watching.
	 *
	 * @var bool
	 */
	protected $watching = false;

	/**
	 * Create a new watcher instance.
	 *
	 * @param  \JasonLewis\ResourceWatcher\Tracker  $tracker
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(Tracker $tracker, Filesystem $files)
	{
		$this->tracker = $tracker;
		$this->files = $files;
	}

	/**
	 * Register a resource to be watched.
	 *
	 * @param  string  $resource
	 * @return \JasonLewis\ResourceWatcher\Listener
	 */
	public function watch($resource)
	{
		if ( ! $this->files->exists($resource))
		{
			throw new RuntimeException('Resource must exist before you can watch it.');
		}
		elseif ($this->files->isDirectory($resource))
		{
			$resource = new DirectoryResource(new SplFileInfo($resource), $this->files);

			$resource->setupDirectory();
		}
		else
		{
			$resource = new FileResource(new SplFileInfo($resource), $this->files);
		}

		// The listener gives users the ability to bind listeners on the events
		// created when watching a file or directory. We'll give the listener
		// to the tracker so the tracker can fire any bound listeners.
		$listener = new Listener;

		$this->tracker->register($resource, $listener);

		return $listener;
	}

	/**
	 * Start watching for a given interval. The interval and timeout and measured
	 * in microseconds, so 1,000,000 microseconds is equal to 1 second.
	 *
	 * @param  int  $interval
	 * @param  int  $timeout
	 * @param  \Closure  $callback
	 * @return void
	 */
	public function startWatch($interval = 1000000, $timeout = null, Closure $callback = null)
	{
		$this->watching = true;

		$timeWatching = 0;

		while ($this->watching)
		{
			if (is_callable($callback))
			{
				call_user_func($callback, $this);
			}

			usleep($interval);

			$this->tracker->checkTrackings();

			$timeWatching += $interval;

			if ( ! is_null($timeout) and $timeWatching >= $timeout)
			{
				$this->stopWatch();
			}
		}
	}

	/**
	 * Alias of startWatch.
	 *
	 * @param  int  $interval
	 * @param  int  $timeout
	 * @param  \Closure  $callback
	 * @return void
	 */
	public function start($interval = 1000000, $timeout = null, Closure $callback = null)
	{
		$this->startWatch($interval, $timeout, $callback);
	}

	/**
	 * Get the tracker instance.
	 *
	 * @return \JasonLewis\ResourceWatcher\Tracker
	 */
	public function getTracker()
	{
		return $this->tracker;
	}

	/**
	 * Stop watching.
	 *
	 * @return void
	 */
	public function stopWatch()
	{
		$this->watching = false;
	}

	/**
	 * Alias of stopWatch.
	 *
	 * @return void
	 */
	public function stop()
	{
		$this->stopWatch();
	}

	/**
	 * Determine if watcher is watching.
	 *
	 * @return bool
	 */
	public function isWatching()
	{
		return $this->watching;
	}

}