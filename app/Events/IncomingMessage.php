<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class IncomingMessage implements ShouldBroadcast {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public $channel;

    /**
     * IncomingMessage constructor.
     * @param $messageToSend
     * @param $roomId
     */

    public function __construct($messageToSend, $roomId)
    {
        $this->message = $messageToSend;

        $this->channel = 'messages-channel.' . $roomId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return PresenceChannel
     */

    public function broadcastOn()
    {
        return new PresenceChannel($this->channel);
    }
}
