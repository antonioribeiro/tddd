<?php

namespace PragmaRX\TestsWatcher\Package\Data\Repositories\Support;

use Illuminate\Support\Collection;

trait Messages
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $messages;

    /**
     * Get all messages.
     *
     * @return Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Add a message to the list.
     *
     * @param $type
     * @param $body
     *
     * @internal param $string
     * @internal param $string1
     */
    protected function addMessage($body, $type = 'line')
    {
        $this->messages->push(['type' => $type, 'body' => $body]);
    }

    /**
     * Set messages.
     *
     * @param \Illuminate\Support\Collection $messages
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }
}
