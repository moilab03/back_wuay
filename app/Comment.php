<?php

namespace App;

use App\Transformers\CommentTransformer;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'group_user_id',
        'comment'
    ];

    protected $guarded = ['id'];

    public $transformer = CommentTransformer::class;

    function setData($attributes)
    {
        $data['group_user_id'] = $attributes['group_user_id'];
        $data['comment'] = $attributes['comment'];
        return $data;
    }
}
