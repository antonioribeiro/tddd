<?php

namespace PragmaRX\TestsWatcher\Package\Services;

class Config
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Get a configuration key.
     *
     * @param $key
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function get($key)
    {
        $this->loadConfig();

        if (is_null($value = array_get($this->config, $key))) {
            throw new \Exception("The configuration key '{$key}' was not defined.");
        }

        return $value;
    }

    protected function loadConfig()
    {
        if (is_null($this->config)) {
            $this->config = config('ci');
        }
    }

    /**
     * Set the config.
     *
     * @param array $config
     */
    public function set($config)
    {
        $this->config = $config;
    }
}
