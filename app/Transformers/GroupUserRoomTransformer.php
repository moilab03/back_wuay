<?php

namespace App\Transformers;

use App\GroupUser;
use App\GroupUserRoom;
use App\Status;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class GroupUserRoomTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'groupSender',
        'groupReceiver'
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];


    function includeGroupSender(GroupUserRoom $groupUserRoom)
    {
        $groupSender = GroupUser::find($groupUserRoom->sender_group_id);
        if ($groupSender)
            return $this->item($groupSender, new GroupUserTransformer());
    }


    function includeGroupReceiver(GroupUserRoom $groupUserRoom)
    {
        $groupSender = GroupUser::find($groupUserRoom->receiver_group_id);
        if ($groupSender)
            return $this->item($groupSender, new GroupUserTransformer());
    }


    function getStatus($groupUserRoom)
    {
        $status = $groupUserRoom->status()->first();
        return [
            'id' => $status->id,
            'status' => $status->status,
        ];
    }
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(GroupUserRoom $groupUserRoom)
    {
        return [
            'id' => $groupUserRoom->id,
            'status' => $this->getStatus($groupUserRoom)
        ];
    }
}
