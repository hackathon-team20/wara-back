<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:8|max:30'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation error: ' . $e->getMessage()], 422);
        }

        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();

                if ($user->is_admin) {
                    // 管理者用のレスポンス
                    $token = $user->createToken("{$user->id}",['admin'])->plainTextToken;
                    return response()->json(['token' => $token, 'message' => 'Admin logged in'], 200);
                } else {
                    // 一般ユーザー用のレスポンス
                    $token = $user->createToken("{$user->id}",['user'])->plainTextToken;
                    return response()->json(['token' => $token, 'message' => 'User logged in'], 200);
                }
            } else {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Authentication error: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }
}