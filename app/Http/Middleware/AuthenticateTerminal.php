<?php

namespace App\Http\Middleware;

use App\Models\Plant;
use App\Models\WorkCenter;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AuthenticateTerminal extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        $segments = $request->segments();
        if (count($segments) < 3)
            abort(404);

        $plantUid = $segments[1];
        $workCenterUid = $segments[2];

        if (!$request->expectsJson()) {
            return route('terminal.login', [$plantUid, $workCenterUid]);
        }
    }
}
