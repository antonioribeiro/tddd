<?php

namespace PragmaRX\TestsWatcher\Package\Notifications\Channels;

use Illuminate\Notifications\Messages\MailMessage;

class Mail extends BaseChannel
{
    /**
     * @param $notifiable
     * @param $item
     *
     * @return $this
     */
    public function send($notifiable, $item)
    {
        return (new MailMessage())
            ->line($this->getMessage($item))
            ->from(
                __config('notifications.from.address'),
                __config('notifications.from.name')
            )
            ->action($this->getActionTitle(), $this->getActionLink());
    }
}
