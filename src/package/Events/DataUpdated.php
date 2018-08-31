<?php

namespace PragmaRX\Tddd\Package\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DataUpdated implements ShouldBroadcast
{
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('tddd');
    }

    public function broadcastAs()
    {
        return 'tddd:data-updated';
    }
}
