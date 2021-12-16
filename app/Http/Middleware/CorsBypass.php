<?php

namespace App\Http\Middleware;

use Closure;

class CorsBypass
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
        $headers["Access-Control-Allow-Origin"] = "*";
        $headers["Access-Control-Allow-Methods"] = "PUT, PATCH, POST, DELETE, GET, OPTIONS";
        $headers["Access-Control-Allow-Headers"] = "Accept, Authorization, Content-Type";
        return $next($request);
    }
}
