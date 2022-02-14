<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Commerce;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Category\CategoryPostRequest;

class CategoryStoreController extends ApiController
{

    protected $category;

    public function __construct(Category $category)
    {
        $this->middleware('commerce');
        $this->category = $category;
    }


    /**
     * @OA\Post(
     *     path="/api/v1/categories/{commerce}",
     *     summary="Crea una categoria asociado a un comercio",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"category"},
     *                 @OA\Property(
     *                     property="category",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una categoria",
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
    function store(CategoryPostRequest $request, Commerce $commerce)
    {
        try {
            $commerce->validateUserAdministrator();
            $this->category = $this->category->create(
                $this->category->setData($request, $commerce->id)
            );
            return $this->showOne($this->category, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
