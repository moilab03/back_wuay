<?php

namespace App\Observers;

use App\Events\InvitationGroupUser;
use App\GroupUserRoom;

class GroupUserRoomObserver
{
    /**
     * Handle the group user room "created" event.
     *
     * @param  \App\GroupUserRoom  $groupUserRoom
     * @return void
     */
    public function created(GroupUserRoom $groupUserRoom)
    {
        event(new InvitationGroupUser($groupUserRoom));
    }

    /**
     * Handle the group user room "updated" event.
     *
     * @param  \App\GroupUserRoom  $groupUserRoom
     * @return void
     */
    public function updated(GroupUserRoom $groupUserRoom)
    {
        //
    }

    /**
     * Handle the group user room "deleted" event.
     *
     * @param  \App\GroupUserRoom  $groupUserRoom
     * @return void
     */
    public function deleted(GroupUserRoom $groupUserRoom)
    {
        //
    }

    /**
     * Handle the group user room "restored" event.
     *
     * @param  \App\GroupUserRoom  $groupUserRoom
     * @return void
     */
    public function restored(GroupUserRoom $groupUserRoom)
    {
        //
    }

    /**
     * Handle the group user room "force deleted" event.
     *
     * @param  \App\GroupUserRoom  $groupUserRoom
     * @return void
     */
    public function forceDeleted(GroupUserRoom $groupUserRoom)
    {
        //
    }
}
