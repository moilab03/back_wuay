<?php

namespace App\Http\Controllers\GroupUserRoom;

use App\GroupUser;
use App\GroupUserRoom;
use App\Http\Controllers\ApiController;
use App\Status;


class GroupUserRoomUpdateController extends ApiController
{

    public function __construct()
    {
        $this->middleware('jwt:api');
    }

    /**
     * @OA\Put(
     *     path="/api/groupUserRooms/accept/{group_user_room}",
     *     summary="Acepta una invitacion a una sala",
     *     tags={"Group user room"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una sala de grupo",
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
    function acceptGroupUser(GroupUserRoom $groupUserRoom)
    {
        try {
            $this->validateStatus(Status::INVITED, $groupUserRoom->status_id);
            $this->validateUser($groupUserRoom->receiver_group_id);
            $groupUserRoom = $groupUserRoom->updateAccepted();
            $groupUserRoom->save();
            return $this->showOne($groupUserRoom,200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/groupUserRooms/rejected/{group_user_room}",
     *     summary="Rechaza una invitación de sala",
     *     tags={"Group user room"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna un mensaje de eliminación",
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
    function rejectGroupUser(GroupUserRoom $groupUserRoom)
    {
        try {
            $this->validateStatus(Status::INVITED, $groupUserRoom->status_id);
            $this->validateUser($groupUserRoom->receiver_group_id);
            $groupUserRoom->delete();
            return $this->showMessage('Rechazado',200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    protected function validateUser($id)
    {
        $userReceived = GroupUser::find($id)->user_id;
        if ($userReceived !== auth()->user()->id)
            throw new \Exception('No puedes realizar acciones sobre esta sala, no eres el administrador del grupo');
    }

    protected function validateStatus($status, $statusGroup)
    {
        $id = Status::byStatus($status)->value('id');
        if ($id !== $statusGroup)
            throw new \Exception('La sala no tiene el estado correcto para realizar esta acción');
    }
}
