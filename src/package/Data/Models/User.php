<?php

namespace PragmaRX\TestsWatcher\Package\Data\Models;

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

    /**
     * Route notifications for e-mail.
     *
     * @return string
     */
    private function routeNotificationForEmail()
    {
        return __config('notifications.user.email');
    }

    /**
     * Route notifications for slack.
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    private function routeNotificationForSlack()
    {
        return config('services.slack.webhook_url');
    }
}
