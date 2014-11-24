<?php namespace JasonLewis\ResourceWatcher\Resource;

use SplFileInfo;
use JasonLewis\ResourceWatcher\Event;
use Illuminate\Filesystem\Filesystem;

class FileResource implements ResourceInterface {

	/**
	 * SplFileInfo resource.
	 *
	 * @var \SplFileInfo
	 */
	protected $resource;

	/**
	 * Path to the resource.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Illuminate filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * Resources last modified timestamp.
	 *
	 * @var int
	 */
	protected $lastModified;

	/**
	 * Indicates whether the resource exists or not.
	 *
	 * @var bool
	 */
	protected $exists = true;

	/**
	 * Create a new resource instance.
	 *
	 * @param  \SplFileInfo  $resource
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(SplFileInfo $resource, Filesystem $files)
	{
		$this->resource = $resource;
		$this->path = $resource->getRealPath();
		$this->files = $files;
		$this->exists = $this->files->exists($this->path);
		$this->lastModified = ! $this->exists ?: $this->files->lastModified($this->path);
	}

	/**
	 * Detect any changes to the resource.
	 *
	 * @return array
	 */
	public function detectChanges()
	{
		clearstatcache(true, $this->path);

		if ( ! $this->exists and $this->files->exists($this->path))
		{
			$this->lastModified = $this->files->lastModified($this->path);
			$this->exists = true;

			return array(new Event($this, Event::RESOURCE_CREATED));
		}
		elseif ($this->exists and ! $this->files->exists($this->path))
		{
			$this->exists = false;

			return array(new Event($this, Event::RESOURCE_DELETED));
		}
		elseif ($this->exists and $this->isModified())
		{
			$this->lastModified = $this->files->lastModified($this->path);

			return array(new Event($this, Event::RESOURCE_MODIFIED));
		}

		return array();
	}

	/**
	 * Determine if the resource has been modified.
	 *
	 * @return bool
	 */
	public function isModified()
	{
		return $this->lastModified < $this->files->lastModified($this->path);
	}

	/**
	 * Get the resource key.
	 *
	 * @return string
	 */
	public function getKey()
	{
		return md5($this->path);
	}

	/**
	 * Get the path of the resource.
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Get the resource SplFileInfo.
	 *
	 * @return \SplFileInfo
	 */
	public function getSplFileInfo()
	{
		return $this->resource;
	}

	/**
	 * Get the resources last modified timestamp.
	 *
	 * @return int
	 */
	public function getLastModified()
	{
		return $this->lastModified;
	}

}