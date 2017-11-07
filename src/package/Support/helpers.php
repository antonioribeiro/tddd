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
        return \PragmaRX\TestsWatcher\Package\Facades\Config::get($key, $default);
    }
}

if (!function_exists('replace_laravel_paths')) {
    /**
     * Replace laravel paths strings.
     *
     * @return string
     */
    function replace_laravel_paths($string)
    {
        return str_replace(
            [
                '{{ laravel.app.path }}',
                '{{ laravel.base.path }}',
                '{{ laravel.config.path }}',
                '{{ laravel.storage.path }}',
            ],

            [
                app_path(),
                base_path(),
                config_path(),
                storage_path(),
            ],

            $string
        );
    }
}


if (!function_exists('is_arrayable')) {
    /**
     * Check if a variable is arrayable.
     *
     * @param mixed $id
     *
     * @return boolean
     */
    function is_arrayable($id)
    {
        return (is_array($id) ||
            $id instanceof \Illuminate\Contracts\Support\Arrayable);
    }
}

