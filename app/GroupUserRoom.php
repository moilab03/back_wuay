<?php

namespace App;

use App\Transformers\GroupUserRoomTransformer;
use Illuminate\Database\Eloquent\Model;

class GroupUserRoom extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'receiver_group_id',
        'status_id',
        'sender_group_id'
    ];


    protected $casts = [
        'receiver_group_id' => 'integer',
        'status_id' => 'integer',
        'sender_group_id' => 'integer'
    ];

    public $transformer = GroupUserRoomTransformer::class;

    function setData($groupReceiver)
    {
        $groupUser = GroupUser::byUser(auth()->user()->id)
            ->byCommerce(auth()->user()->current_commerce_id)
            ->byStatus(Status::ENABLED)
            ->value('id');

        $data['status_id'] = Status::byStatus(Status::INVITED)->value('id');
        $data['sender_group_id'] = $groupUser;
        $data['receiver_group_id'] = $groupReceiver;
        return $data;
    }

    function updateAccepted()
    {
        $this->status_id = Status::byStatus(Status::ACCEPTED)->value('id');
        return $this;
    }


    function updateFinalized()
    {
        $this->status_id = Status::byStatus(Status::FINALIZED)->value('id');
        return $this;
    }

    function updateRejected()
    {
        $this->status_id = Status::byStatus(Status::REJECTED)->value('id');
        return $this;
    }

    function status()
    {
        return $this->belongsTo(Status::class);
    }

    function scopeMyActualGroup($query, $id)
    {
        return $query->where('receiver_group_id', $id)->orWhere('sender_group_id', $id);
    }

    function scopeByReceiver($query, $id)
    {
        return $query->where('receiver_group_id', $id);
    }

    function scopeBySender($query, $id)
    {
        return $query->where('sender_group_id', $id);
    }

    function scopeNotStatuses($query, $statuses)
    {
        $ids = Status::whereIn('status', $statuses)->pluck('id');
        return $query->whereNotIn('status_id', $ids);
    }

    function scopeByStatuses($query, $statuses)
    {
        $ids = Status::whereIn('status', $statuses)->pluck('id');
        return $query->whereIn('status_id', $ids);
    }
}
