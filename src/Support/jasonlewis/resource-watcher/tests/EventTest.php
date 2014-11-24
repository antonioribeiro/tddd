<?php

use Mockery as m;
use JasonLewis\ResourceWatcher\Event;

class EventTest extends PHPUnit_Framework_TestCase {


	public function tearDown()
	{
		m::close();
	}


	public function testCanGetEventCode()
	{
		$resource = m::mock('JasonLewis\ResourceWatcher\Resource\ResourceInterface');
		$event = new Event($resource, Event::RESOURCE_CREATED);
		$this->assertEquals(Event::RESOURCE_CREATED, $event->getCode());
	}


	public function testCanGetResourceFromEvent()
	{
		$resource = m::mock('JasonLewis\ResourceWatcher\Resource\ResourceInterface');
		$event = new Event($resource, Event::RESOURCE_CREATED);
		$this->assertInstanceOf('JasonLewis\ResourceWatcher\Resource\ResourceInterface', $event->getResource());
	}


}