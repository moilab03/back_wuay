<?php

namespace App\Transformers;

use App\GroupUser;
use App\Status;
use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{

    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [
        'commerce'
    ];


    public function includeCommerce(User $user)
    {
        $commerce = $user->commerce()->first();
        if ($commerce)
            return $this->item($commerce, new CommerceTransformer());
    }

    function getStatus($user)
    {
        $status = $user->status()->first();
        return [
            'id' => $status->id,
            'status' => $status->status,
            'is_enabled' => $status->status === Status::ENABLED,
            'label' => $status->status === Status::ENABLED ? 'Activo' : 'Inactivo'
        ];
    }

    function getRol($user)
    {
        $rol = $user->rol()->first();
        return [
            'id' => $rol->id,
            'rol' => $rol->rol
        ];
    }


    protected function createGroup($user)
    {
        if ($user->principal_group_user_id){
            $group = GroupUser::find($user->principal_group_user_id);
            GroupUser::create([
                'name' => $group->name,
                'user_id' => $user->id,
                'status_id' => Status::byStatus(Status::ENABLED)->value('id'),
                'commerce_id' => $user->current_commerce_id
            ]);
        }
    }

    public function transform(User $user)
    {
        $needGroup = GroupUser::byUser($user->id)
                ->byCommerce($user->current_commerce_id)->count() === 0;

        if ($needGroup) {
            $this->createGroup($user);
        }
        return [
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'status' => $this->getStatus($user),
            'rol' => $this->getRol($user),
            'has_commerce' => $user->commerce()->count() > 0,
            'terms_and_conditions' => $user->terms_and_conditions,
            'current_commerce' => $user->current_commerce_id,
            'need_create_group' => GroupUser::byUser($user->id)
                    ->byCommerce($user->current_commerce_id)->count() === 0,
            'principal_group_user_id' => $user->principal_group_user_id
        ];
    }
}
