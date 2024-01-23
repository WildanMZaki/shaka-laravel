<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KasbonController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.kasbon.index');
    }
}
