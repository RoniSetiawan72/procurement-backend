<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success'   => false,
                'message'   => 'Kredensial tidak valid. Silahkan periksa email dan password Anda.'
            ], 401);
        }

        $token = $user->createToken('procurement_auth_token')->plainTextToken;

        return response()->json([
            'success'   => true,
            'message'   => 'Login Berhasil',
            'data'      => [
                'user'  => $user,
                'token' => $token
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success'   => true,
            'message'   => 'Logout berhasil. Token telah dicabut.'
        ], 200);
    }
}
