<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function mine(Request $request)
    {
        $user_id = $request->attributes->get('user_id');
    }
}
