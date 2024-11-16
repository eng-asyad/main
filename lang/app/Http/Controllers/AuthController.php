<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $formFields = $request->validate([
            'name' => 'required|unique:users',
            'password' => 'required|min:8',
            'phone_number' => 'required|unique:users'
        ]);
        $token = bcrypt($request['name']);
        $formFields['token'] = $token;
        User::create($formFields);
        return response()->json([
            'message' => 'success',
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $formFields = $request->validate([
            'name' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('name', $formFields['name'])->first();

        if ($user && Hash::check($formFields['password'], $user['password'])) {
            Auth::login($user);
            return response()->json([
                'message' => 'success',
                'token' => $user['token']
            ]);
        } else {
            return response()->json([
                'message' => 'error'
            ]);
        }
    }
    public function who()
    {
        $user = User::find(auth()->id());
        return $user['name'];
    }
    public function logout()
    {
        Auth::logout();
        Session::flush();
        return response()->json(['message' => 'success']);
    }
    public function login_admin(Request $request)
    {
        $formFields = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $email = $request['email'];
        $password = $request['password'];
        if ($email == 'admin@admin.com' && $password == 'adminPass') {
            return response()->json([
                'message' => 'success'
            ]);
        } else {
            return response()->json([
                'message' => 'error'
            ]);
        }
    }
}