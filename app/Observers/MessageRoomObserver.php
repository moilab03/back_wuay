<?php

namespace App\Observers;

use App\Events\CommerceMessageSend;
use App\Events\MessageSended;
use App\MessageRoom;


class MessageRoomObserver
{
    /**
     * Handle the message room "created" event.
     *
     * @param \App\MessageRoom $messageRoom
     * @return void
     */
    public function created(MessageRoom $messageRoom)
    {
        if ($messageRoom->group_user_room_id)
            event(new MessageSended($messageRoom));
        else
            event(new CommerceMessageSend($messageRoom));
    }
}
