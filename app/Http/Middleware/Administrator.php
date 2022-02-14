<?php

namespace App\Http\Middleware;

use App\Rol;
use Closure;
use App\Http\Controllers\ApiController;


class Administrator extends ApiController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->rol_id === Rol::byRol(Rol::ADMINISTRATOR)->value('id'))
            return $next($request);
        return $this->errorResponse('No tiene permisos para visualizar este contenido', 400);
    }
}
