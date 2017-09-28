<?php

namespace PragmaRX\TestsWatcher\Events;

use Illuminate\Contracts\Queue\ShouldQueue;

class TestsFailed implements ShouldQueue
{
    /**
     * @var
     */
    public $tests;

    /**
     * @var
     */
    public $channel;

    /**
     * Create a new event instance.
     */
    public function __construct($tests, $channel)
    {
        $this->tests = $tests;

        $this->channel = $channel;
    }
}
