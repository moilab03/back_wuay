<?php

namespace App\Events;

use App\GroupUser;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InvitationGroupUser implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $groupUserRoom;

    /**
     * Create a new event instance.
     *
     * @param $groupUserRoom
     */
    public function __construct($groupUserRoom)
    {
        $this->groupUserRoom = $groupUserRoom;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('groupUser.'.$this->groupUserRoom->receiver_group_id);
    }

    public function broadcastWith()
    {
        $name = GroupUser::find($this->groupUserRoom->sender_group_id)->name;
        return [
            'id' => $this->groupUserRoom->id,
            'name' => $name,
            'group_sender_id' => $this->groupUserRoom->sender_group_id
        ];
    }
}
