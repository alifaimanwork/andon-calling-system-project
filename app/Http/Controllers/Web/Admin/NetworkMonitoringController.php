<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NetworkMonitoringController extends Controller
{
    //TODO: Resource Guard
    public function index(Request $request)
    {
        $viewData = [
            'topBarTitle' => 'I-POS SETTINGS'
        ];
        return view('pages.web.admin.network-monitoring.index', $viewData);
    }
}
