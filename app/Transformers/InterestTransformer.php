<?php

namespace App\Transformers;

use App\GroupInterest;
use App\GroupUser;
use App\Interest;
use App\Status;
use League\Fractal\TransformerAbstract;

class InterestTransformer extends TransformerAbstract
{

    function verifyIfHasAdd($interest, $groupUser)
    {
        if (!$groupUser)
            return false;
        return GroupInterest::byGroupUser($groupUser)
                ->byInterest($interest->id)->count() > 0;
    }

    /**
     * @param Interest $interest
     * @return array
     */
    public function transform(Interest $interest)
    {

        $groupUser = GroupUser::byUser(auth()->user()->id)
            ->byCommerce(auth()->user()->current_commerce_id)
            ->byStatus(Status::ENABLED)
            ->value('id');

        return [
            'id' => $interest->id,
            'name' => $interest->name,
            'is_add' => $this->verifyIfHasAdd($interest, $groupUser)
        ];
    }
}
