<?php

namespace App\Http\Controllers\GroupUserRoom;

use App\GroupUser;
use App\GroupUserRoom;
use App\Http\Controllers\ApiController;
use App\Status;

class GroupUserRoomDeleteController extends ApiController
{

    public function __construct()
    {
        $this->middleware('jwt:api');
    }

    function delete( $room)
    {
        try {
            $room = GroupUserRoom::find($room);
            $this->validateIfCan($room);
            $this->validateStatus($room);
            $room = $room->updateFinalized();
            $room->save();
            return $this->showOne($room, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    function validateStatus($room)
    {
        if (Status::byStatus(Status::ACCEPTED)->value('id') !== $room->status_id) {
            throw new \Exception('No se puede cancelar esta sala');
        }
    }

    function validateIfCan($room)
    {
        $groupUser = GroupUser::byUser(auth()->user()->id)
            ->byCommerce(auth()->user()->current_commerce_id)
            ->byStatus(Status::ENABLED)
            ->value('id');
        if ($room->receiver_group_id === $groupUser || $room->sender_group_id === $groupUser)
            return;
        throw new \Exception('No tienes permiso para cancelar esta sala');
    }
}
