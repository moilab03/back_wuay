<?php

namespace App\Transformers;

use App\GroupInterest;
use App\GroupUser;
use App\GroupUserRoom;
use App\GroupUserSilent;
use App\Interest;
use App\Status;
use App\TypeResource;
use App\User;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;

class GroupUserTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];


    public function getPhotos($user)
    {
        $photos = [];

        GroupUser::find(User::find($user)->principal_group_user_id)->resources()
            ->byTypeResource(TypeResource::PHOTO_GROUP_USER)
            ->get()
            ->map(function ($photo, $key) use (&$photos) {
                $photos[$key]['id'] = $photo->id;
                $photos[$key]['url'] = env('URL_STORAGE') . $photo->url;
            });

        if (empty($photos)) {
            return [
                [
                    'id' => 0,
                    'url' => 'https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/271deea8-e28c-41a3-aaf5-2913f5f48be6/de7834s-6515bd40-8b2c-4dc6-a843-5ac1a95a8b55.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7InBhdGgiOiJcL2ZcLzI3MWRlZWE4LWUyOGMtNDFhMy1hYWY1LTI5MTNmNWY0OGJlNlwvZGU3ODM0cy02NTE1YmQ0MC04YjJjLTRkYzYtYTg0My01YWMxYTk1YThiNTUuanBnIn1dXSwiYXVkIjpbInVybjpzZXJ2aWNlOmZpbGUuZG93bmxvYWQiXX0.BopkDn1ptIwbmcKHdAOlYHyAOOACXW0Zfgbs0-6BY-E'
                ]
            ];
        }

        return $photos;
    }


    protected function getInterests($groupUser)
    {
        return $groupUser->groupInterests()->get()->map(function ($item) {
            return [
                'interest' => Interest::find($item->interest_id)->name
            ];
        });
    }


    protected function getConnection($groupUser)
    {
        $groupAuth = GroupUser::byUser(auth()->user()->id)
            ->byCommerce(auth()->user()->current_commerce_id)
            ->byStatus(Status::ENABLED)
            ->value('id');
        $interestUser = GroupInterest::where('group_user_id', $groupAuth)->pluck('interest_id');
        return $groupUser->groupInterests()
            ->whereIn('interest_id', $interestUser)->get()->map(function ($item) {
                return [
                    'interest' => Interest::find($item->interest_id)->name
                ];
            });
    }

    protected function getGroupRoomId($groupUser)
    {
        $groupAuth = GroupUser::byUser(auth()->user()->id)
            ->byCommerce(auth()->user()->current_commerce_id)
            ->byStatus(Status::ENABLED)
            ->value('id');

        $groupUserRoom = GroupUserRoom::byReceiver($groupUser->id)
            ->bySender($groupAuth)
            ->notStatuses([Status::FINALIZED, Status::REJECTED])
            ->first();

        if ($groupUserRoom) {

            $count = GroupUserSilent::byGroupUser($groupAuth)
                ->byGroupTarget($groupUserRoom->id)
                ->count();
            $status = Status::find($groupUserRoom->status_id);

            return [
                'id' => $groupUserRoom->id,
                'estado' => $status->status,
                'is_silent' => $count > 0,
                'is_invited' => ($status->status === Status::ACCEPTED) || ($status->status === Status::INVITED)
            ];
        }
        return [
            'is_invited' => false,
            'is'
        ];
    }


    protected function getIdSilentGroup($groupUser)
    {

        return GroupUserSilent::byGroupUser($groupUser->id)->pluck('group_user_target_id');
    }


    protected function getIdGroupRoom($groupUser)
    {

        return GroupUserRoom::myActualGroup($groupUser->id)->pluck('id');
    }

    /**
     * @param GroupUser $groupUser
     * @return array
     */
    public function transform(GroupUser $groupUser)
    {
        $principalGroup = GroupUser::find(User::find($groupUser->user_id)->principal_group_user_id);
        return [
            'id' => $groupUser->id,
            'name' => $principalGroup->name,
            'photos' => $this->getPhotos($groupUser->user_id),
            'interests' => $this->getInterests($principalGroup),
            'connection' => (auth()->user() && ($principalGroup->user_id !== auth()->user()->id)) ? $this->getConnection($principalGroup) : [],
            'is_my_group' => auth()->user() ? $groupUser->user_id === auth()->user()->id : false,
            'user' => User::find($groupUser->user_id)->name,
            'group_room' => auth()->user() ? $this->getGroupRoomId($groupUser) : [],
            'silent_groups' => $this->getIdSilentGroup($groupUser),
            'groups_rooms' => $this->getIdGroupRoom($groupUser)
        ];
    }
}
