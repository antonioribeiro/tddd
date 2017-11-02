<?php

namespace PragmaRX\TestsWatcher\Package\Services;

use Illuminate\Http\File;
use PragmaRX\Support\Yaml;

class Config
{
    /**
     * The config.
     *
     * @var array
     */
    protected $config;

    /**
     * @var Yaml
     */
    protected $yaml;

    public function __construct(Yaml $yaml)
    {
        $this->yaml = $yaml;
    }

    /**
     * Check if the config is valid.
     *
     * @return bool
     */
    private function configIsValid()
    {
        return !is_null($this->config);
    }

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

    /**
     * Load the config.
     *
     */
    protected function loadConfig()
    {
        if ($this->configIsValid()) {
            return;
        }

        return $this->loadYamlFiles()->mapWithKeys(function ($value, $key) {
            dd($value);

            return [$this->removeExtension($key) => $value];
        });

        // load
    }

    /**
     * Load resource files.
     *
     * @return \Illuminate\Support\Collection
     */
    private function loadYamlFiles()
    {
        return $this->yaml->loadYamlFromDir($this->getConfigPath());
    }

    /**
     * Get the config path.
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public function getConfigPath()
    {
        return str_replace('{laravel.config.path}', config_path(), config('tddd.config.path'));
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

    /**
     * Invalidate the current config.
     *
     */
    public function invalidateConfig()
    {
        $this->config = null;
    }
}
