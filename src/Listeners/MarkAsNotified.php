<?php

namespace PragmaRX\TestsWatcher\Listeners;

use PragmaRX\TestsWatcher\Data\Repositories\Data;
use PragmaRX\TestsWatcher\Events\UserNotifiedOfFailure;

class MarkAsNotified
{
    public function __construct(Data $dataRepository)
    {
        $this->dataRepository = $dataRepository;
    }

    /**
     * Handle the event.
     *
     * @param UserNotifiedOfFailure $event
     *
     * @return void
     */
    public function handle(UserNotifiedOfFailure $event)
    {
        $this->dataRepository->markTestsAsNotified($event->tests);
    }
}
