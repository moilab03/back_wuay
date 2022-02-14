<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ApiController;
use App\Rol;
use Closure;

class AdministratorCommerce extends ApiController
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() &&
            auth()->user()->rol_id === Rol::byRol(Rol::ADMINISTRATOR_COMMERCE)->value('id'))
            return $next($request);
        return $this->errorResponse('No tiene permisos para visualizar este contenido', 400);
    }
}
