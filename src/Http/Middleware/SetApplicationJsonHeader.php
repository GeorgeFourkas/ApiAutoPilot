<?php

namespace ApiAutoPilot\ApiAutoPilot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetApplicationJsonHeader
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        return $next($request);
    }
}
