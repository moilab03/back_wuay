<?php

namespace App\Transformers;

use App\GroupUser;
use App\GroupUserRoom;
use App\MessageRoom;
use League\Fractal\TransformerAbstract;

class MessageRoomTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'groupUserRoom', 'groupUser'
    ];


    function includeGroupUserRoom(MessageRoom $messageRoom)
    {
        $groupUserRoom = GroupUserRoom::find($messageRoom->group_user_room_id);
        if ($groupUserRoom)
            return $this->item($groupUserRoom, new GroupUserRoomTransformer());
    }

    function includeGroupUser(MessageRoom $messageRoom)
    {
        if (!$messageRoom->group_user_room_id) {
            $groupUser = GroupUser::find($messageRoom->group_sender_id);
            if ($groupUser)
                return $this->item($groupUser, new GroupUserTransformer());
        }
    }

    /**
     * @param MessageRoom $messageRoom
     * @return array
     */
    public function transform(MessageRoom $messageRoom)
    {
        return [
            'id' => $messageRoom->id,
            'message' => $messageRoom->message
        ];
    }
}
