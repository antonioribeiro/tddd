<?php

use Mockery as m;
use JasonLewis\ResourceWatcher\Tracker;

class TrackerTest extends PHPUnit_Framework_TestCase {


	public function tearDown()
	{
		m::close();
	}


	public function testResourceRegisteredWithTracker()
	{
		$resource = m::mock('JasonLewis\ResourceWatcher\Resource\ResourceInterface');
		$resource->shouldReceive('getKey')->twice()->andReturn('foo');
		$listener = m::mock('JasonLewis\ResourceWatcher\Listener');
		$tracker = new Tracker;
		$tracker->register($resource, $listener);
		$this->assertTrue($tracker->isTracked($resource));
	}


}