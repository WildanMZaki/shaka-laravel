<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        if (auth()->check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        $user = User::where('username', $request->username)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'msg' => 'Username atau password salah'
            ]);
        }

        $remember = $request->has('remember') ? true : false;
        auth()->login($user, $remember);

        // return redirect()->route('dashboard');
        return redirect()->intended(route('dashboard'));
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }
}
