<?php

namespace PragmaRX\TestsWatcher\Notifications\Channels;

use Illuminate\Notifications\Messages\SlackMessage;

class Slack extends BaseChannel
{
    /**
     * @param $notifiable
     * @param $tests
     * @return $this
     */
    public function send($notifiable, $tests)
    {
        $notification = (new SlackMessage)
            ->error()
            ->from(
                config('ci.notifications.from.name'),
                config('ci.notifications.from.icon_emoji')
            )
            ->content($this->getMessage($tests));

        $tests->each(function($test) use ($notification) {
            $notification->attachment(function ($attachment) use ($test) {
                $attachment->title($this->makeActionTitle($test), $this->makeActionLink($test));
            });
        });

        return $notification;
    }
}
