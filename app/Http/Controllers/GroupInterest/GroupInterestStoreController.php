<?php

namespace App\Http\Controllers\GroupInterest;

use App\GroupInterest;
use App\GroupUser;
use App\Http\Controllers\ApiController;
use App\Http\Requests\GroupInterest\GroupInterestPostRequest;
use App\Interest;
use App\Status;
use App\Transformers\InterestTransformer;
use Illuminate\Support\Facades\DB;

class GroupInterestStoreController extends ApiController
{
    protected $groupInterest;

    function __construct(GroupInterest $groupInterest)
    {
        $this->middleware('jwt:api');
        $this->groupInterest = $groupInterest;
    }



    /**
     * @OA\Post(
     *     path="/api/groupInterest",
     *     summary="Agrega o actualiza los intereses de grupo",
     *     tags={"Group interest"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"interests"},
     *                 @OA\Property(
     *                     property="interests",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         format="integer",
     *                     ),
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de intereses",
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
    function store(GroupInterestPostRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->deleteIfHasInterests();
            $interests = [];
            foreach ($request->interests as $interest) {
                $interests[] = $this->groupInterest->setData($interest);
            }
            $this->groupInterest->insert($interests);
            DB::commit();
            return $this->showAll(Interest::byStatus(Status::ENABLED)->get(), InterestTransformer::class, 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    function deleteIfHasInterests()
    {
        try {
            $groupUser = GroupUser::byUser(auth()->user()->id)
                ->byCommerce(auth()->user()->current_commerce_id)
                ->byStatus(Status::ENABLED)
                ->value('id');
            return GroupInterest::byGroupUser($groupUser)->delete();
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
