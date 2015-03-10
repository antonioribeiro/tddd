<?php

namespace PragmaRX\Ci\Services;

class Base {

	/**
	 * Configuration keys.
	 *
	 * @var
	 */
	private $config;

	/**
	 * Get a configuration key.
	 *
	 * @param $key
	 * @return mixed
	 * @throws \Exception
	 */
	protected function getConfig($key)
	{
		if ( ! isset($this->config[$key]))
		{
			throw new \Exception("The configuration key '{$key}' was not defined.");
		}

		return $this->config[$key];
	}

	/**
	 * Set the configuration array.
	 *
	 * @param $config
	 * @return mixed
	 */
	public function setConfig($config)
	{
		return $this->config = $config;
	}

}
