<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthRouteApi extends Controller
{
    public function index(Request $request) {
        return $request->user();
    }

    public function login(Request $request) {
        return redirect('login');
    }
}
