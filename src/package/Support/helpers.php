<?php

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
     * @return bool
     */
    function is_arrayable($id)
    {
        return is_array($id) ||
            $id instanceof \Illuminate\Contracts\Support\Arrayable;
    }
}

if (!function_exists('replace_suite_paths')) {
    /**
     * Replace suite paths strings.
     *
     * @return string
     */
    function replace_suite_paths($suite, $string)
    {
        return str_replace('{$project_path}', $suite->project->path, $string);
    }
}
