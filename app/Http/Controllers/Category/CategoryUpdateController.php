<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Commerce;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Category\CategoryPostRequest;

class CategoryUpdateController extends ApiController
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->middleware('commerce');
        $this->category = $category;
    }



    /**
     * @OA\Put(
     *     path="/api/v1/categories/{category}/{commerce}",
     *     summary="Actualiza una categoria del comercio",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"category", "_method"},
     *                 @OA\Property(
     *                     property="category",
     *                     type="string",
     *                 ),
     *      @OA\Property(
     *                     property="_method",
     *                     type="string",
     *     enum={"PUT"}
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
    function update(CategoryPostRequest $request, Category $category, Commerce $commerce)
    {
        try {
            $commerce->validateUserAdministrator();
            $category = $category->updateData($request);
            $category->save();
            return $this->showOne($category, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/categories/status/{category}/{commerce}",
     *     summary="Cambia el estado de una categoria",
     *     tags={"Categories"},
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
    function changeState(Category $category, Commerce $commerce)
    {
        try {
            $commerce->validateUserAdministrator();
            $category = $category->changeStatus();
            $category->save();
            return $this->showOne($category, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
