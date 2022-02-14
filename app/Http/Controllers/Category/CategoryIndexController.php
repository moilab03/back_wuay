<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Commerce;
use App\Http\Controllers\ApiController;
use App\Status;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Request;


class CategoryIndexController extends ApiController
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->middleware('commerce')
            ->only(['indexForCommerce', 'indexCategories']);
        $this->category = $category;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/categories/commerce/{commerce}?page={position}",
     *     summary="Trae la lista de categorias asociadas a mi comercio",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de categorias",
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
    function indexForCommerce(Request $request, Commerce $commerce)
    {
        try {
            $commerce->validateUserAdministrator();
            // $quantity = $request->get('quantity', 15);
            $this->category = $this->category
                ->byCommerce($commerce->id)
                ->get();
            return $this->showAll($this->category, CategoryTransformer::class, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/categories/user/{commerce}",
     *     summary="Trae la lista de categorias asociadas a un comercio habilitadas para un usuario",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de categorias",
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
    function indexForUser(Commerce $commerce)
    {
        try {
            $this->category = $this->category
                ->byCommerce($commerce->id)
                ->byStatus(Status::byStatus(Status::ENABLED)->value('id'))
                ->inRandomOrder()
                ->get();
            return $this->showAll($this->category, CategoryTransformer::class, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/categories/bank",
     *     summary="Trae la lista de categorias asociadas al banco de productos",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna una lista de categorias",
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
    function indexCategories()
    {
        try {
            $id = Commerce::find(8)->id;
            return $this->showAll($this->category->where('commerce_id', $id)->get(), CategoryTransformer::class, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
