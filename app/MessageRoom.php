<?php

namespace App;

use App\Transformers\MessageRoomTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * @property mixed id
 * @property mixed message
 */
class MessageRoom extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'group_user_room_id',
        'group_sender_id',
        'group_receiver_id',
        'message'
    ];

    protected $casts = [
        'group_user_room_id' => 'integer',
        'group_sender_id' => 'integer',
        'group_receiver_id' => 'integer'
    ];

    public $transformer = MessageRoomTransformer::class;

    function setData($attributes)
    {
        $groupUser = GroupUser::byUser(auth()->user()->id)
            ->byCommerce(auth()->user()->current_commerce_id)
            ->byStatus(Status::ENABLED)
            ->value('id');

        $groupUserRoom = GroupUserRoom::find($attributes['group_user_room_id']);

        $data['group_user_room_id'] = $attributes['group_user_room_id'];
        $data['message'] = $attributes['message'];
        $data['group_sender_id'] = $groupUser;
        if ($groupUserRoom) {
            $data['group_receiver_id'] = $groupUserRoom->sender_group_id === $groupUser ? $groupUserRoom->receiver_group_id : $groupUserRoom->sender_group_id;
        }
        return $data;
    }

    function scopeByGroupUserRoom($query, $id)
    {
        return $query->where('group_user_room_id', $id);
    }
}
