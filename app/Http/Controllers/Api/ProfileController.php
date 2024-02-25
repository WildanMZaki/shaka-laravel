<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function change(Request $request)
    {
        $user = $request->attributes->get('user');

        if ($request->has('name')) {
            $user->name = $request->input('name');
        }

        if ($request->hasFile('photo')) {
            $dir = 'employees';
            $path = $request->file('photo')->store("public/$dir");
            $fixPath = $dir . '/' . basename($path);
            if ($user->photo) {
                Storage::delete('public/' . $user->photo);
            }
            $user->photo = $fixPath;
        }

        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Profile brhasil diupdate',
            'name' => $user->name,
            'photo' => $user->photoPath(),
        ]);
    }
}
