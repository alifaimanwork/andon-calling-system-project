<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\Plant;
use Illuminate\Http\Request;

class AuthWeb
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
        if (!isset($request->plant_uid))
            abort(404);

        if (!($request->user()->role <= User::ROLE_USER)) {
            abort(403);
        }

        if ($request->user()->role == User::ROLE_PLANT_ADMIN) {
            $plantIds = User::getCurrent()->getAdminPlants()->pluck('uid')->toArray();

            if (!in_array($request->plant_uid, $plantIds)) {
                abort(403);
            }
        }

        $plant = Plant::where('uid', '=', $request->plant_uid)->firstOrFail();
        $plant->loadAppDatabase();

        if ($request->user()->role == User::ROLE_USER) {
            $web_access = $request->user()->plants()->where('plant_id', '=', $plant->id)->first()->pivot->web_permission ?? 0;
            if ($web_access == 0) {
                abort(403);
            }
        }

        return $next($request);
    }
}
