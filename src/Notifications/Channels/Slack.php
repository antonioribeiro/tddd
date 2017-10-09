<?php

namespace PragmaRX\TestsWatcher\Notifications\Channels;

use Illuminate\Notifications\Messages\SlackMessage;

class Slack extends BaseChannel
{
    /**
     * @param $notifiable
     * @param $tests
     *
     * @return $this
     */
    public function send($notifiable, $tests)
    {
        $notification = (new SlackMessage())
            ->error()
            ->from(
                config('ci.notifications.from.name'),
                $icon = (config('ci.notifications.from.icon_emoji') ?: null)
            )
            ->content($this->getMessage($tests));

        if (is_null($icon)) {
            $notification->image(config('ci.notifications.from.icon_url') ?: null);
        }

        $tests->each(function ($test) use ($notification) {
            $notification->attachment(function ($attachment) use ($test) {
                $attachment->title($this->makeActionTitle($test), $this->makeActionLink($test));
            });
        });

        return $notification;
    }
}
