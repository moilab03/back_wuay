<?php

namespace App\Broadcasting;

use App\GroupUser;
use App\Status;
use App\User;

class GroupUserChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param \App\User $user
     * @return array|bool
     */
    public function join(User $user, $id)
    {
        $groupUser = GroupUser::byUser($user->id)
            ->byCommerce($user->current_commerce_id)
            ->byStatus(Status::ENABLED)
            ->value('id');
        return $groupUser === (int)$id;
    }
}
