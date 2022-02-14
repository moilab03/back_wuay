<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupInterest extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['interest_id', 'group_user_id'];

    public $timestamps = false;

    function setData($interest)
    {
        $groupUser = GroupUser::byUser(auth()->user()->id)
            ->byCommerce(auth()->user()->current_commerce_id)
            ->byStatus(Status::ENABLED)
            ->value('id');
        $data['interest_id'] = $interest;
        $data['group_user_id'] = $groupUser;
        return $data;
    }

    function scopeByGroupUser($query, $id)
    {
        return $query->where('group_user_id', $id);
    }

    function scopeByInterest($query, $id)
    {
        return $query->where('interest_id', $id);
    }
}
