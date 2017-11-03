<?php

namespace PragmaRX\TestsWatcher\Package\Services;

use Illuminate\Support\Collection;
use PragmaRX\Support\Yaml;

class Config
{
    /**
     * The config.
     *
     * @var array
     */
    protected $config = [];

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
        return !is_null($this->config) && $this->config !== [];
    }

    /**
     * Get a configuration key.
     *
     * @param $key
     *
     * @param mixed|null $default
     * @return mixed
     * @throws \Exception
     */
    public function get($key, $default = null)
    {
        $this->loadConfig();

        if (is_null($value = array_get($this->config, $key, $default))) {
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

        $this->config = $this->yaml->loadYamlFilesFromDir($this->getConfigPath())->toArray();
    }

    /**
     * Get a list of all config files.
     *
     * @return Collection
     */
    public function getConfigFiles()
    {
        return $this->yaml->listYamlFilesFromDir($this->getConfigPath())->flatten();
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
     * Set the config item.
     *
     * @param $data
     */
    public function set($data)
    {
        $this->config = array_merge($data, $this->config);
    }

    /**
     * Invalidate the current config.
     */
    public function invalidateConfig()
    {
        $this->config = [];
    }

    /**
     * Check if a file is a config file.
     *
     * @param $file
     * @return boolean
     */
    public function isConfigFile($file)
    {
        return $this->getConfigFiles()->contains($file);
    }
}
