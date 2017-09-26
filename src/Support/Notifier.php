<?php

namespace PragmaRX\Ci\Support;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;

class Notifier
{
    /**
     * Are notifications enabled?
     *
     * @return bool
     */
    private static function enabled()
    {
        return config('ci.notifications.enabled') == true;
    }

    /**
     * Send a notification.
     *
     * @param $title
     * @param $body
     * @param null $icon
     * @return bool
     */
    public static function notify($title, $body, $icon = null)
    {
        if (!static::enabled()) {
            return false;
        }

        $notifier = NotifierFactory::create();

        $notification =
            (new Notification())
                ->setTitle($title)
                ->setBody($body)
        ;

        if (!is_null($icon)) {
            $notification->setIcon('http://vjeantet.fr/images/logo.png');
        }

        $notifier->send($notification);
    }
}
