<?php

namespace App\Events;

use App\Room;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserLeftRoom implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;

    public $roomId;

    public $userName;

    /**
     * Create a new event instance.
     *
     * UserLeftRoom constructor.
     * @param Room $room
     * @param $userName
     *
     */
    public function __construct(Room $room, $userName)
    {
        $this->room = $room;

        $this->roomId = $room->id;

        $this->userName = $userName;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return ['data' => ['roomId' => $this->roomId, 'userName' => $this->userName]];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {

        $channels = [];

        foreach ($this->room->users as $user)
        {

            array_push($channels, new PrivateChannel('user.' . $user->id));

        }
        return $channels;

    }
}
