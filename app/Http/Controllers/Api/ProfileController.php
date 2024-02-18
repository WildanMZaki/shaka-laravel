<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function change(Request $request)
    {
        $user = $request->attributes->get('user');
    }
}
