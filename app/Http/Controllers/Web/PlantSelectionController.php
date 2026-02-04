<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;

class PlantSelectionController extends Controller
{
    public function index(Request $request)
    {
        //TODO: fetch real plant data from database & apply permission
        $regions = Region::with('plants')->get();

        $user = User::getCurrent();

        $viewData = [
            'topBarTitle' => '',
            'regions' => $regions,
            'user' => $user,
        ];

        if ($user->role <= User::ROLE_PLANT_ADMIN) {
            return view('pages.web.plant-selection.index', $viewData);
        }

        if ($user->plants()->where('web_permission', 1)->exists())
            return view('pages.web.plant-selection.index', $viewData);

        $request->session()->invalidate();
        return redirect()->route('login')->withErrors(['error' => 'You are not authorized to access this site.']);
    }
}
