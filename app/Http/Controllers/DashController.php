<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;

class DashController extends Controller
{
    public function index()
    {
        $data['targetSPGFreelancer'] = Settings::of('Target Jual Harian SPG Freelancer');
        $data['sallesment'] = User::whereIn('access_id', [6, 7])->get();
        return view('dash.dash', $data);
    }
}
