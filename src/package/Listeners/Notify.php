<?php

namespace PragmaRX\TestsWatcher\Package\Listeners;

use Notification;
use PragmaRX\TestsWatcher\Package\Events\TestsFailed;
use PragmaRX\TestsWatcher\Package\Notifications\Status;

class Notify
{
    /**
     * @return static
     */
    private function getNotifiableUsers()
    {
        return collect(config('ci.notifications.users.emails'))->map(function ($item) {
            $model = instantiate(config('ci.notifications.users.model'));

            $model->email = $item;

            return $model;
        });
    }

    /**
     * Handle the event.
     *
     * @param TestsFailed $event
     *
     * @return void
     */
    public function handle(TestsFailed $event)
    {
        Notification::send(
            $this->getNotifiableUsers(),
            new Status($event->tests, $event->channel)
        );
    }
}
