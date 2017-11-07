<?php

namespace PragmaRX\TestsWatcher\Package\Services;

use Illuminate\Support\Collection;
use PragmaRX\Support\YamlConfig;

class Config
{
    /**
     * The config.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @var YamlConfig
     */
    protected $yaml;

    /**
     * The config path.
     *
     * @var array
     */
    protected $configPath;

    public function __construct(YamlConfig $yaml)
    {
        $this->yaml = $yaml;
    }

    /**
     * Check if the config is valid.
     *
     * @return bool
     */
    protected function configIsValid()
    {
        return is_array($this->config) && count($this->config) > 0;
    }

    /**
     * Get a configuration key.
     *
     * @param $key
     * @param mixed|null $default
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $this->loadConfig();

        return config("tddd.{$key}", $default);
    }

    /**
     * Load the config.
     */
    public function loadConfig()
    {
        if ($this->configIsValid()) {
            return;
        }

        $this->yaml->loadToConfig($this->getConfigPath(), 'tddd', true)->toArray();
    }

    /**
     * Force the config to be reloaded.
     */
    public function reloadConfig()
    {
        $this->invalidateConfig();

        $this->loadConfig();
    }

    /**
     * Get a list of all config files.
     *
     * @return Collection
     */
    public function getConfigFiles()
    {
        return $this->yaml->listFiles($this->getConfigPath())->flatten();
    }

    /**
     * Get the config path.
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public function getConfigPath()
    {
        if (is_null($this->configPath)) {
            $this->configPath = replace_laravel_paths(config('tddd-base.path'));
        }

        return $this->configPath;
    }

    /**
     * Set the config item.
     *
     * @param $data
     */
    public function set($data)
    {
        $this->config = array_merge($data, $this->config);

        $this->mergeWithLaravelConfig();
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
     *
     * @return bool
     */
    public function isConfigFile($file)
    {
        return $this->getConfigFiles()->contains($file);
    }
}
