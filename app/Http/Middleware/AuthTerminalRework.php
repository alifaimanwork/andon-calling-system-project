<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;

class AuthTerminalRework
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!isset($request->plant_uid, $request->work_center_uid))
            abort(404);

        $user = User::getCurrent();
        if (!$user->isTerminalRework($request->plant_uid, $request->work_center_uid)){
            session(['access_type' => 'terminal']);
            session(['work_center_uid' => $request->work_center_uid]);
            session(['plant_uid' => $request->plant_uid]);
            abort(403);
        }

        return $next($request);
    }
}
