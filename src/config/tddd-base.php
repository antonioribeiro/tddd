<?php

/**
 * This is the Laravel configuration for the package, you only need to tell TDDD where the
 * yaml config files are.
 */
return [
    'path' => env('TDDD_CONFIG_PATH', __DIR__),

    'host_os' => env('TDDD_HOST_OS', __DIR__),

    'user_home' => env('HOME', __DIR__),
];
