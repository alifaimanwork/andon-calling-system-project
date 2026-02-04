<?php

namespace App\Http\Controllers\Terminal\Auth;

use App\Events\Terminal\TerminalUserLoginEvent;
use App\Events\Terminal\TerminalUserLogoutEvent;
use App\Extras\Traits\WorkCenterTrait;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Terminal\TerminalController;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\MonitorClient;
use App\Models\Plant;
use App\Models\User;
use App\Models\WorkCenter;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    use WorkCenterTrait;
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request, $plantUid, $workCenterUid)
    {
        //IPOS Terminal Login
        $viewData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        if (is_null($viewData))
            abort(404);




        return view('pages.terminal.auth.login', $viewData);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request, $plantUid, $workCenterUid)
    {
        $request->authenticate();
        $request->session()->regenerate();

        $viewData = $this->getPlantWorkCenter($plantUid, $workCenterUid);

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $viewData['workCenter'];
        $user = User::getCurrent();

        $allow_access = $user->isTerminalAccessible($plantUid, $workCenterUid);

        if (!$allow_access) {
            //logout user with error message
            $request->session()->invalidate();
            return redirect()->route('terminal.login', [$plantUid, $workCenterUid])->withErrors(['error' => 'You are not authorized to access this work center.']);
        }

        event(new TerminalUserLoginEvent($user, $workCenter));

        return redirect()->intended(route('terminal.index', [$viewData['plant']->uid, $viewData['workCenter']->uid]));
    }

    public function logout(Request $request, $plantUid, $workCenterUid)
    {
        $viewData = $this->getPlantWorkCenter($plantUid, $workCenterUid);
        return view('pages.terminal.auth.logout', $viewData);
    }
    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $plantUid, $workCenterUid)
    {
        $viewData = $this->getPlantWorkCenter($plantUid, $workCenterUid);

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $viewData['workCenter'];
        $user = User::getCurrent();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        event(new TerminalUserLogoutEvent($user, $workCenter));

        return redirect()->route('terminal.login', [$plantUid, $workCenterUid]);
    }
}
