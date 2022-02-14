<?php

namespace App\Broadcasting;

use App\User;
use Illuminate\Support\Facades\Log;

class CommerceChannel
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
        return $user->current_commerce_id === (int)$id;
    }
}
