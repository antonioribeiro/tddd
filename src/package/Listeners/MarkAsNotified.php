<?php

namespace PragmaRX\Tddd\Package\Listeners;

use PragmaRX\Tddd\Package\Data\Repositories\Data;
use PragmaRX\Tddd\Package\Events\UserNotifiedOfFailure;

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
