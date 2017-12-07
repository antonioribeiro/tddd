<?php

namespace PragmaRX\TestsWatcher\Package\Facades;

use Illuminate\Support\Facades\Facade;

class Config extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tddd.config';
    }
}
