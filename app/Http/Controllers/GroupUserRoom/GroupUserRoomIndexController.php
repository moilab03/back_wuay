<?php

namespace App\Http\Controllers\GroupUserRoom;

use App\GroupUser;
use App\GroupUserRoom;
use App\Http\Controllers\ApiController;
use App\Status;
use App\Transformers\GroupUserRoomTransformer;

class GroupUserRoomIndexController extends ApiController
{
    protected $groupUserRoom;

    function __construct(GroupUserRoom $room)
    {
        $this->middleware('jwt:api');
        $this->groupUserRoom = $room;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/groupUserRooms",
     *     summary="Trae la lista de grupos creados, aceptados o invitados",
     *     tags={"Group user room"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de salas de grupo",
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
    function index()
    {
        try {
            $groupUser = GroupUser::byUser(auth()->user()->id)
                ->byCommerce(auth()->user()->current_commerce_id)
                ->byStatus(Status::ENABLED)
                ->value('id');
            $this->groupUserRoom = $this->groupUserRoom->myActualGroup($groupUser)
                ->get();
            return $this->showAll($this->groupUserRoom, GroupUserRoomTransformer::class, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
