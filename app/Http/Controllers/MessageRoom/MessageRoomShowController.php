<?php

namespace App\Http\Controllers\MessageRoom;

use App\GroupUser;
use App\GroupUserSilent;
use App\Http\Controllers\ApiController;
use App\MessageRoom;
use App\Status;

class MessageRoomShowController extends ApiController
{

    public function __construct()
    {
        $this->middleware('jwt:api');
    }


    function show($id)
    {
        try {
            $messageRoom = MessageRoom::find($id);
            $groupUser = GroupUser::byUser(auth()->user()->id)
                ->byCommerce(auth()->user()->current_commerce_id)
                ->byStatus(Status::ENABLED)
                ->value('id');
            $count = GroupUserSilent::where('group_user_id', $groupUser)
                ->where('group_user_target_id', $messageRoom->group_sender_id)->count();
            if ($count === 0)
                return $this->showOne($messageRoom, 200);
            else return $this->errorResponse('Grupo silenciado', 400);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

}
