<?php

use Mockery as m;
use JasonLewis\ResourceWatcher\Event;
use JasonLewis\ResourceWatcher\Resource\FileResource;

class FileResourceTest extends PHPUnit_Framework_TestCase {


	public function tearDown()
	{
		m::close();
	}


	public function testCanGetResourceAttributes()
	{
		$files = m::mock('Illuminate\Filesystem\Filesystem');
		$files->shouldReceive('exists')->once()->andReturn(true);
		$files->shouldReceive('lastModified')->twice()->andReturn($modified = time());

		$resource = new FileResource(new SplFileInfo(__FILE__), $files);

		$this->assertEquals(md5(__FILE__), $resource->getKey());
		$this->assertEquals(__FILE__, $resource->getPath());
		$this->assertEquals($modified, $resource->getLastModified());
		$this->assertFalse($resource->isModified());
	}


	public function testDetectingOfResourceCreated()
	{
		$files = m::mock('Illuminate\Filesystem\Filesystem');
		$files->shouldReceive('exists')->once()->andReturn(false);
		$files->shouldReceive('exists')->once()->andReturn(true);
		$files->shouldReceive('lastModified')->once()->andReturn(time());

		$resource = new FileResource(new SplFileInfo(__FILE__), $files);

		$events = $resource->detectChanges();
		$this->assertInstanceOf('JasonLewis\ResourceWatcher\Event', $event = array_pop($events));
		$this->assertEquals(Event::RESOURCE_CREATED, $event->getCode());
	}


	public function testDetectingOfResourceModified()
	{
		$files = m::mock('Illuminate\Filesystem\Filesystem');
		$files->shouldReceive('exists')->twice()->andReturn(true);
		$files->shouldReceive('lastModified')->times(3)->andReturn(time(), time() + 3600);

		$resource = new FileResource(new SplFileInfo(__FILE__), $files);

		$events = $resource->detectChanges();
		$this->assertInstanceOf('JasonLewis\ResourceWatcher\Event', $event = array_pop($events));
		$this->assertEquals(Event::RESOURCE_MODIFIED, $event->getCode());
	}


	public function testDetectingOfResourceDeleted()
	{
		$files = m::mock('Illuminate\Filesystem\Filesystem');
		$files->shouldReceive('exists')->once()->andReturn(true);
		$files->shouldReceive('exists')->once()->andReturn(false);
		$files->shouldReceive('lastModified')->once()->andReturn(time());

		$resource = new FileResource(new SplFileInfo(__FILE__), $files);

		$events = $resource->detectChanges();
		$this->assertInstanceOf('JasonLewis\ResourceWatcher\Event', $event = array_pop($events));
		$this->assertEquals(Event::RESOURCE_DELETED, $event->getCode());
	}


}