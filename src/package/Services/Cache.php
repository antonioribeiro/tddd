<?php

namespace PragmaRX\Tddd\Package\Services;

class Cache extends Base
{
    /**
     * The cache instance.
     *
     * @var array
     */
    protected $cache;

    /**
     * Put a value to the cache store
     *
     * @return mixed
     * @throws \Exception
     */
    public function put($key, $value, $minutes = 525600)
    {
        $this->getCacheInstance()->put($key, $value, $minutes);

        return $value;
    }

    /**
     * Get a value from the cache store
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($key)
    {
        $cached = $this->getCacheInstance()->get($key);

        return $cached;
    }

    /**
     * Get the cache instance.
     *
     * @return array|\Illuminate\Foundation\Application|mixed
     * @throws \Exception
     */
    protected function getCacheInstance()
    {
        if (!$this->cache) {
            $this->cache = app($this->config('root.cache.instance'));
        }

        return $this->cache;
    }
}
