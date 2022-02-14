<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;

class ApiController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Tnder API Documentation",
     *      @OA\Contact(
     *          email="fabianportillo97@gmail.com"
     *      ),
     * )
     */
    public function __construct()
    {
        //
    }
}
