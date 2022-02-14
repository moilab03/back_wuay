<?php

namespace App\Transformers;

use App\Comment;
use App\GroupUser;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'groupUser'
    ];


    function includeGroupUser(Comment $comment)
    {
        return $this->item(GroupUser::find($comment->group_user_id), new GroupUserTransformer());
    }


    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Comment $comment)
    {
        return [
            'id' => $comment->id,
            'comment' => $comment->comment
        ];
    }
}
