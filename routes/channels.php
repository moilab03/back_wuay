<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});


Broadcast::channel('room.{GroupUserRoomId}', \App\Broadcasting\RoomChannel::class);
Broadcast::channel('commerce.{commerceId}', \App\Broadcasting\CommerceChannel::class);
Broadcast::channel('groupUser.{groupUser}', \App\Broadcasting\GroupUserChannel::class);
