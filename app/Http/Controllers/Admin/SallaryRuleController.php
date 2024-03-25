<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SallaryRuleController extends Controller
{
    public function index()
    {
        $data = [];
        return view('admin.sallary-rule.index', $data);
    }
}
