<?php

namespace App\Events;

use App\Room;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewRoomCreated implements ShouldBroadcast
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;

    /**
     * Create a new event instance.
     *
     * NewRoomCreated constructor.
     * @param $room
     */

    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $channels = [];

        foreach ($this->room->users as $user) {
            if ($user->name !== auth()->user()->name) {
                array_push($channels, new PrivateChannel('user.' . $user->id));
            }
        }

        return $channels;
    }
}
