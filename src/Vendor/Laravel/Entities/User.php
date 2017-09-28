<?php

namespace PragmaRX\TestsWatcher\Vendor\Laravel\Entities;

class User extends Model
{
    /**
     * Route notifications for the Email channel.
     *
     * @return string
     */
    public function routeNotificationFor($for)
    {
        if ($for == 'slack') {
            return $this->routeNotificationForSlack();
        }

        return $this->routeNotificationForEmail();
    }

    private function routeNotificationForEmail()
    {
        return config('ci.notifications.user.email');
    }

    private function routeNotificationForSlack()
    {
        return config('services.slack.webhook_url');
    }
}
