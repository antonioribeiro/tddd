<?php

use Mockery as m;
use JasonLewis\ResourceWatcher\Watcher;
use JasonLewis\ResourceWatcher\Tracker;

class WatcherTest extends PHPUnit_Framework_TestCase {


	public function setUp()
	{
		$this->tracker = new Tracker;
		$this->files = m::mock('Illuminate\Filesystem\Filesystem');
		$this->watcher = new Watcher($this->tracker, $this->files);
	}


	public function tearDown()
	{
		m::close();
	}


	/**
	 * @expectedException \RuntimeException
	 */
	public function testWatchingNonExistentResourceThrowsException()
	{
		$this->files->shouldReceive('exists')->once()->andReturn(false);

		$this->watcher->watch(__DIR__);
	}


	public function testWatchDirectoryResource()
	{
		$this->files->shouldReceive('exists')->times(8)->andReturn(true);
		$this->files->shouldReceive('isDirectory')->once()->andReturn(true);
		$this->files->shouldReceive('lastModified')->times(7)->andReturn(time());

		$this->watcher->watch(__DIR__);

		$tracked = $this->watcher->getTracker()->getTracked();
		$resource = array_pop($tracked);
		$this->assertInstanceOf('JasonLewis\ResourceWatcher\Resource\DirectoryResource', $resource[0]);
	}


	public function testWatchFileResource()
	{
		$this->files->shouldReceive('exists')->twice()->andReturn(true);
		$this->files->shouldReceive('isDirectory')->once()->andReturn(false);
		$this->files->shouldReceive('lastModified')->once()->andReturn(time());
		$this->watcher->watch(__DIR__);
		$tracked = $this->watcher->getTracker()->getTracked();
		$resource = array_pop($tracked);
		$this->assertInstanceOf('JasonLewis\ResourceWatcher\Resource\FileResource', $resource[0]);
	}


	public function testWatchCanBeStarted()
	{
		$this->files->shouldReceive('exists')->times(3)->andReturn(true);
		$this->files->shouldReceive('isDirectory')->once()->andReturn(false);
		$this->files->shouldReceive('lastModified')->times(2)->andReturn(time());

		$this->watcher->watch(__DIR__);

		$startTime = time();
		$this->watcher->startWatch(1000000, 1000000);

		$this->assertEquals(1, time() - $startTime);
	}


	public function testCanGetTrackerInstance()
	{
		$this->assertInstanceOf('JasonLewis\ResourceWatcher\Tracker', $this->watcher->getTracker());
	}


	public function testTrackingsAreChecked()
	{
		$this->watcher = new Watcher($this->tracker, new Illuminate\Filesystem\Filesystem);

		touch(__DIR__.'/mock.file');

		$listener = $this->watcher->watch(__DIR__.'/mock.file');
		$this->assertInstanceOf('JasonLewis\ResourceWatcher\Listener', $listener);

		$created = $modified = $deleted = $anything = false;

		$listener->onCreate(function($resource) use (&$created)
		{
			$created = true;
		});

		$listener->onModify(function($resource) use (&$modified)
		{
			$modified = true;
		});

		$listener->onDelete(function($resource) use (&$deleted)
		{
			$deleted = true;
		});

		$listener->onAnything(function($event, $resource) use (&$anything)
		{
			$anything = true;
		});

		$iterations = 0;

		$this->watcher->startWatch(10000, 30000, function($watcher) use (&$iterations)
		{
			if ($iterations == 0)
			{
				touch(__DIR__.'/mock.file', time() + 3600);
			}

			if ($iterations == 1)
			{
				unlink(__DIR__.'/mock.file');
			}

			if ($iterations == 2)
			{
				touch(__DIR__.'/mock.file');
			}

			$iterations++;
		});

		unlink(__DIR__.'/mock.file');

		$this->assertTrue($created);
		$this->assertTrue($modified);
		$this->assertTrue($deleted);
		$this->assertTrue($anything);
	}

}