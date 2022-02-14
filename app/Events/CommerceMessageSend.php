<?php

namespace App\Events;

use App\GroupUser;
use App\Transformers\MessageRoomTransformer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommerceMessageSend implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $messageRoom;

    /**
     * CommerceMessageSend constructor.
     * @param $messageRoom
     */
    public function __construct($messageRoom)
    {
        $this->messageRoom = $messageRoom;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $id = GroupUser::find($this->messageRoom->group_sender_id)->commerce_id;
        return new PrivateChannel('commerce.' . $id);
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
