<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupUserSilent extends Model
{
    protected $guarded = ['id'];

    protected $fillable = ['group_user_id', 'group_user_target_id'];

    function setData($attributes)
    {
        $data['group_user_target_id'] = $attributes['group_user_target_id'];
        $data['group_user_id'] = $attributes['group_user_id'];
        return $data;
    }

    function scopeByGroupUser($query, $id)
    {
        return $query->where('group_user_id',$id);
    }

    function scopeByGroupTarget($query, $id)
    {
        return $query->where('group_user_target_id', $id);
    }

}
