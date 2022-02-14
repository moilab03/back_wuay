<?php

namespace App\Events;

use App\Transformers\MessageRoomTransformer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSended implements ShouldBroadcast
{
    use SerializesModels, Dispatchable, InteractsWithSockets;

    public $messageRoom;

    public function __construct($messageRoom)
    {
        $this->messageRoom = $messageRoom;
    }


    public function broadcastOn()
    {
        return new PrivateChannel('room.' . $this->messageRoom->group_user_room_id);
    }

    public function broadcastWith()
    {
        return $this->transformData($this->messageRoom, MessageRoomTransformer::class);
    }


    protected function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);
        return $transformation->toArray();
    }

}
