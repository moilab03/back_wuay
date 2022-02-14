<?php

namespace App\Broadcasting;

use App\GroupUser;
use App\GroupUserRoom;
use App\Status;
use App\User;
use Illuminate\Support\Facades\Log;

class RoomChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\User  $user
     * @return array|bool
     */
    public function join(User $user, $room)
    {

        $groupUser = GroupUser::byUser($user->id)
            ->byCommerce($user->current_commerce_id)
            ->byStatus(Status::ENABLED)
            ->value('id');

        $groupUserRoom = GroupUserRoom::find($room);
        return ($groupUserRoom->receiver_group_id === $groupUser) || ($groupUserRoom->sender_group_id === $groupUser);
    }
}
