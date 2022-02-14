<?php

namespace App\Http\Controllers\MessageRoom;

use App\Commerce;
use App\GroupUser;
use App\GroupUserRoom;
use App\GroupUserSilent;
use App\Http\Controllers\ApiController;
use App\MessageRoom;
use App\Status;
use App\Transformers\MessageRoomTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageRoomIndexController extends ApiController
{
    protected $messageRoom;

    function __construct(MessageRoom $room)
    {
        $this->middleware('jwt:api');
        $this->messageRoom = $room;
    }


    function getMessagesRooms(Request $request)
    {
        try {
            $groupUser = GroupUser::byUser(auth()->user()->id)
                ->byCommerce(auth()->user()->current_commerce_id)
                ->byStatus(Status::ENABLED)
                ->value('id');
            $rooms = GroupUserRoom::myActualGroup($groupUser)
                ->byStatuses([Status::ACCEPTED])
                ->pluck('id');
            $quantity = $request->get('quantity', 20);
            $this->messageRoom = $this->messageRoom->whereIn('group_user_room_id', $rooms)
                ->latest('id')
                ->paginate($quantity);
            return $this->showAll($this->messageRoom,MessageRoomTransformer::class,200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/roomMessages/{group_user_room}?page=1,2,3",
     *     summary="Trae la lista de mensajes de una sala",
     *     tags={"Room message"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de mensajes",
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
    function indexRoom(Request $request, $room)
    {
        try {
            $room = GroupUserRoom::find($room);
            $this->validateIfCanRoom($room);
            $quantity = $request->get('quantity', 20);
            $this->messageRoom = $this->messageRoom
                ->byGroupUserRoom($room->id)
                ->latest('id')
                ->paginate($quantity);
            return $this->showAll($this->messageRoom, MessageRoomTransformer::class, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/roomMessages/public/{commerce}?page=1,2,3",
     *     summary="Trae la lista de mensajes de un comercio",
     *     tags={"Room message"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de mensajes",
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

    function indexCommerce(Request $request, Commerce $commerce)
    {
        try {
            $this->validateIfCanView($commerce->id);

            $ids = GroupUser::byCommerce($commerce->id)
                ->byStatus(Status::ENABLED)
                ->pluck('id');

            $groupUser = GroupUser::byUser(auth()->user()->id)
                ->byCommerce(auth()->user()->current_commerce_id)
                ->byStatus(Status::ENABLED)
                ->value('id');

            $idSilent = GroupUserSilent::byGroupUser($groupUser)->pluck('group_user_target_id');

            $quantity = $request->get('quantity', 20);

            $this->messageRoom = $this->messageRoom
                ->whereIn('group_sender_id', $ids)
                ->whereNotIn('group_sender_id', $idSilent)
                ->whereNull('group_user_room_id')
                ->latest('id')
                ->paginate($quantity);
            return $this->showAll($this->messageRoom, MessageRoomTransformer::class, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    function validateIfCanView($id)
    {
        if (auth()->user()->current_commerce_id !== $id) {
            throw new \Exception('No puedes ver este contenido, no estas en el comercio');
        }
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    function validateIfCanRoom($room)
    {
        $groupUser = GroupUser::byUser(auth()->user()->id)
            ->byCommerce(auth()->user()->current_commerce_id)
            ->byStatus(Status::ENABLED)
            ->value('id');

        if (($room->receiver_group_id === $groupUser) || ($room->sender_group_id === $groupUser)) {
            return true;
        }
        throw new \Exception('No perteneces a esta sala, no puedes enviar mensajes');
    }
}
