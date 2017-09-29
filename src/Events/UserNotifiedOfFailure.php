<?php

namespace PragmaRX\TestsWatcher\Events;

use Illuminate\Contracts\Queue\ShouldQueue;

class UserNotifiedOfFailure implements ShouldQueue
{
    /**
     * @var
     */
    public $tests;

    /**
     * Create a new event instance.
     */
    public function __construct($tests)
    {
        $this->tests = $tests;
    }
}
