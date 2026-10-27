<?php

namespace App\Modules\Core\Http\Middleware;

use App\Exceptions\RestrictedAreaException;
use Closure;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param string
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if(auth()->user() === null) {
            abort(401);
        }

        if(auth()->user()->roles->isEmpty() || auth()->user()->permission($permission)->count() < 1) {
            throw new RestrictedAreaException();
        }


        return $next($request);
    }
}
