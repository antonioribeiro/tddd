<?php

if (!function_exists('__config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array|string $key
     * @param mixed        $default
     *
     * @return mixed|\Illuminate\Config\Repository
     */
    function __config($key = null, $default = null)
    {
        if (is_null($key)) {
            return \PragmaRX\TestsWatcher\Package\Facades\Config::all();
        }

        if (is_array($key)) {
            return \PragmaRX\TestsWatcher\Package\Facades\Config::set($key);
        }

        return \PragmaRX\TestsWatcher\Package\Facades\Config::get($key, $default);
    }
}
