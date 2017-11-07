<?php

/**
 * This is the Laravel configuration for the package, you only need to tell TDDD where the
 * yaml config files are.
 */
return [
    'path' => env('APP_CONFIG_PATH', __DIR__),

    'user_home' => env('HOME', __DIR__),
];
