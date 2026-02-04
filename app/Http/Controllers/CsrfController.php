<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CsrfController extends Controller
{
    public function getToken(Request $request)
    {
        return ['_token' => csrf_token()];
    }
}
