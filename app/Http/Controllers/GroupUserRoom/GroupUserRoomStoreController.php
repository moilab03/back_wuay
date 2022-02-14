<?php

namespace App\Http\Controllers\GroupUserRoom;

use App\GroupUser;
use App\GroupUserRoom;
use App\Http\Controllers\ApiController;
use App\Http\Requests\GroupUserRoom\GroupUserRoomPostRequest;
use App\Status;
use Illuminate\Support\Facades\Log;

class GroupUserRoomStoreController extends ApiController
{
    protected $groupUserRoom;

    public function __construct(GroupUserRoom $groupUserRoom)
    {
        $this->middleware('jwt:api');
        $this->groupUserRoom = $groupUserRoom;
    }


    /**
     * @OA\Post(
     *     path="/api/groupUserRooms",
     *     summary="Envia una invitación a un grupo",
     *     tags={"Group user room"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"group_user_id"},
     *                 @OA\Property(
     *                     property="group_user_id",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retorna un sala de grupo",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en validaciones de negocio.",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Entidad no procesable.",
     *     ),
     *     security={ {"bearer_token": {}} },
     * )
     */
    function store(GroupUserRoomPostRequest $request)
    {
        try {
            $this->validateIfHasRoom($request->group_user_id, $request->group_user_id);
            $this->groupUserRoom = $this->groupUserRoom->create(
                $this->groupUserRoom->setData($request->group_user_id)
            );
            return $this->showOne($this->groupUserRoom, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    protected function validateIfHasRoom($id, $target)
    {
        $groupUser = GroupUser::byUser(auth()->user()->id)
            ->byCommerce(auth()->user()->current_commerce_id)
            ->byStatus(Status::ENABLED)
            ->value('id');
        if ((int)$target === $groupUser) {
            throw new \Exception('Este es tu grupo no puedes invitarlo');
        }
        if(!$groupUser)
            throw new \Exception('No tienes creado un grupo');

        if ($this->groupUserRoom
                ->bySender($groupUser)
                ->byReceiver($id)
                ->byStatuses([Status::INVITED, Status::ACCEPTED])->count() > 0
        )
            throw new \Exception('Ya tienes creada una sala con este grupo');
    }
}
