<?php

namespace PragmaRX\TestsWatcher\Notifications\Channels;

interface Contract
{
    /**
     * @param $notifiable
     * @param $item
     *
     * @return mixed
     */
    public function send($notifiable, $item);
}
