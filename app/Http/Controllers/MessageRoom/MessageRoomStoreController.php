<?php

namespace App\Http\Controllers\MessageRoom;

use App\Events\MessageSended;
use App\GroupUser;
use App\GroupUserRoom;
use App\Http\Controllers\ApiController;
use App\Http\Requests\MessageRoom\MessageRoomPostRequest;
use App\MessageRoom;
use App\Status;

class MessageRoomStoreController extends ApiController
{
    private $messageRoom;

    public function __construct(MessageRoom $messageRoom)
    {
        $this->middleware('jwt:api');
        $this->messageRoom = $messageRoom;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/roomMessages",
     *     summary="Envia un mensaje publico o privado a una sala",
     *     tags={"Room message"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"message", "is_public"},
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                 ),
     *      @OA\Property(
     *                     property="is_public",
     *                     type="boolean",
     *                 ),
     *      @OA\Property(
     *                     property="group_user_room_id",
     *                     type="string",
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retorna un mensaje de sala o un mensaje publico, emite mensaje por medio de socket",
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
    function store(MessageRoomPostRequest $postRequest)
    {
        try {
            if (!$postRequest->is_public) {
                $this->validateIfCan($postRequest->group_user_room_id);
                $this->validateIfCanPublic();
            }
            if ($postRequest->is_public) {
                $this->validateIfCanPublic();
            }
            $this->messageRoom = $this->messageRoom->create($this->messageRoom->setData($postRequest));
            return $this->showOne($this->messageRoom, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    function validateIfCanPublic()
    {
        $groupUser = GroupUser::byUser(auth()->user()->id)
            ->byCommerce(auth()->user()->current_commerce_id)
            ->byStatus(Status::ENABLED)
            ->count();
        if ($groupUser === 0)
            throw new \Exception('No tienes un grupo creado');
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    function validateIfCan($id)
    {
        $groupUser = GroupUser::byUser(auth()->user()->id)
            ->byCommerce(auth()->user()->current_commerce_id)
            ->byStatus(Status::ENABLED)
            ->value('id');

        $groupUserRoom = GroupUserRoom::find($id);
        if (($groupUserRoom->receiver_group_id === $groupUser) || ($groupUserRoom->sender_group_id === $groupUser)) {
            return true;
        }
        throw new \Exception('No perteneces a esta sala, no puedes enviar mensajes');
    }
}
