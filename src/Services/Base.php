<?php

namespace PragmaRX\TestsWatcher\Services;

class Base
{
	/**
	 * Get a configuration key.
	 *
	 * @param $key
	 * @return mixed
	 * @throws \Exception
	 */
	protected function getConfig($key)
	{
		if (is_null($value = config("ci.{$key}")))
		{
			throw new \Exception("The configuration key '{$key}' was not defined.");
		}

		return $value;
	}
}
